<?php

declare(strict_types=1);

namespace Netgen\Bundle\InformationCollectionBundle\Command;

use DateInterval;
use DateTimeImmutable;
use Exception;
use Netgen\InformationCollection\API\Service\InformationCollection;
use Netgen\InformationCollection\API\Value;
use Netgen\InformationCollection\API\Value\Filter\ContentId;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\HelpCommand;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

use function array_filter;
use function array_map;
use function array_unique;
use function count;
use function explode;
use function implode;
use function is_array;
use function is_string;
use function sprintf;

use const PHP_INT_MAX;

final class DeleteOldCollectedInfoCommand extends Command
{
    protected static $defaultName = 'nginfocollector:delete';

    /**
     * @var DateInterval
     */
    private $period;

    /**
     * @var InformationCollection
     */
    private  $infoCollection;

    public function __construct(InformationCollection $infoCollection)
    {
        $this->infoCollection = $infoCollection;
        // Parent constructor call is mandatory for commands registered as services
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('nginfocollector:delete');
        $this->setDescription('Deletes collected data that is older than specified period.');
        $this->setHelp('This command allows you to delete data collected by this library that is older than specified period.');

        $this->setDefinition(
            new InputDefinition(
                [
                    new InputOption('content-id', 'c', InputOption::VALUE_REQUIRED, 'Content id.'),
                    new InputOption('field-identifiers', 'f', InputOption::VALUE_REQUIRED, 'Field definition identifiers list.'),
                    new InputOption('period', 'p', InputOption::VALUE_REQUIRED, 'Attributes older that this period will be deleted.'),
                    new InputOption('all', 'a', InputOption::VALUE_NONE, 'Delete all fields.'),
                    new InputOption('neglect', 'nn', InputOption::VALUE_NONE, 'Do not ask for confirmation.'),
                ],
            ),
        );

        $this->addUsage('--content-id=123 --field-identifiers=title,name,last_name');
        $this->addUsage('--info-collection-id=456 --field-identifiers=title,name,last_name');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (null === $input->getOption('content-id')) {
            $output->writeln('<error>                                       </error>');
            $output->writeln('<error>     Missing content-id parameter.     </error>');
            $output->writeln('<error>                                       </error>');

            return $this->displayHelp($input, $output);
        }

        if (null === $input->getOption('field-identifiers') && !$input->getOption('all')) {
            $output->writeln('<error>                                              </error>');
            $output->writeln('<error>     Missing field-identifiers parameter.     </error>');
            $output->writeln('<error>                                              </error>');

            return $this->displayHelp($input, $output);
        }

        if (null === $input->getOption('period')) {
            $output->writeln('<error>                                       </error>');
            $output->writeln('<error>     Missing period parameter.         </error>');
            $output->writeln('<error>                                       </error>');

            return $this->displayHelp($input, $output);
        }

        $contentId = (int) $input->getOption('content-id');
        $fields = $this->getFields($input);

        $info = sprintf('Command will delete <info>%s</info> fields for content #%d', empty($fields) ? 'all' : implode(', ', $fields), $contentId);
        $output->writeln($info);

        if ($this->proceedWithAction($input, $output)) {
            $output->write('<info>Running.... </info>');

            $filterCriteria = new Value\Filter\FilterCriteria(
                new ContentId($contentId, 0, PHP_INT_MAX),
                new DateTimeImmutable('@0'),
                $this->getDateFromPeriod(),
            );
            $collections = $this->infoCollection->filterCollections($filterCriteria);

            $extractIdFunction = function($collection) {
                return $collection->getId();
            };

            $collectionsIds = array_map($extractIdFunction, $collections->getCollections());
            $filterCollections = new Value\Filter\Collections($contentId, $collectionsIds);

            $this->infoCollection->deleteCollections($filterCollections);
            $count = count($filterCollections->getCollectionIds());
            $output->writeln('<info>Done.</info>');
            $output->writeln("<info>Deleted #{$count} collections.</info>");

            return 0;
        }

        $output->writeln('<info>Canceled.</info>');
        return 0;
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        if (!empty($input->getOption('period'))) {
            try {
                $period = $input->getOption('period');
                if (is_string($period)) {
                    $this->period = new DateInterval($period);
                }
            } catch (Exception $exception) {
                $output->writeln('Please enter valid DateInterval string.');

                exit(0);
            }
        }
    }

    private function displayHelp(InputInterface $input, OutputInterface $output)
    {
        $help = new HelpCommand();
        $help->setCommand($this);

        return $help->run($input, $output);
    }

    private function getFields(InputInterface $input)
    {
        if (!empty($input->getOption('all'))) {
            return [];
        }

        if (null !== $input->getOption('field-identifiers')) {
            $ids = [];
            $fieldIdentifiers = $input->getOption('field-identifiers');

            if (is_string($fieldIdentifiers)) {
                $ids = explode(',', $fieldIdentifiers);
            }

            if (is_array($fieldIdentifiers)) {
                $ids = array_filter($fieldIdentifiers);
            }

            return array_unique((array) $ids);
        }

        return [];
    }

    private function proceedWithAction(InputInterface $input, OutputInterface $output)
    {
        if ($input->getOption('neglect')) {
            return true;
        }
        $helper = $this->getHelper('question');
        $question = new ConfirmationQuestion('Continue with this action? y/n ', false, '/^(y|j)/i');

        if ($helper->ask($input, $output, $question)) {
            return true;
        }

        return false;
    }

    private function getDateFromPeriod(): DateTimeImmutable
    {
        $dt = new DateTimeImmutable();

        return $dt->sub($this->period);
    }
}
