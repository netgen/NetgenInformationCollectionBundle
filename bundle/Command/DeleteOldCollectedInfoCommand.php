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
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Console\Style\StyleInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
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
    private $infoCollection;

    /**
     * @var StyleInterface
     */
    private $io;

    public function __construct(InformationCollection $infoCollection)
    {
        $this->infoCollection = $infoCollection;
        // Parent constructor call is mandatory for commands registered as services
        parent::__construct();
    }

    protected function configure(): void
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
                ],
            ),
        );

        $this->addUsage('--content-id=123 --field-identifiers=title,name,last_name');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io = new SymfonyStyle($input, $output);

        if ($input->getOption('content-id') === null) {
            $this->io->error('Missing content-id parameter.');
        }

        if ($input->getOption('field-identifiers') === null && !$input->getOption('all')) {
            $this->io->error('Missing field-identifiers parameter.');
        }

        if ($input->getOption('period') === null) {
            $this->io->error('Missing period parameter.');
        }

        $contentId = (int) $input->getOption('content-id');
        $fields = $this->getFields($input);

        $info = sprintf('Command will delete %s fields for content #%d', count($fields) === 0 ? 'all' : implode(', ', $fields), $contentId);
        $this->io->info($info);

        if ($this->io->confirm('Continue with this action? y/n ', false)){
            $this->io->info('Running....');

            $filterCriteria = new Value\Filter\FilterCriteria(
                new ContentId($contentId, 0, PHP_INT_MAX),
                new DateTimeImmutable('@0'),
                $this->getDateFromPeriod(),
            );
            $collections = $this->infoCollection->filterCollections($filterCriteria);

            $collectionsIds = array_map(function($collection) {
                return $collection->getId();
            }, $collections->getCollections());
            $filterCollections = new Value\Filter\Collections($contentId, $collectionsIds);

            $this->infoCollection->deleteCollections($filterCollections);
            $count = count($filterCollections->getCollectionIds());
            $this->io->info('Done.');
            $this->io->info("Deleted #{$count} collections.");

            return 0;
        }

        $this->io->info('Canceled.');

        return 0;
    }

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
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

    private function getFields(InputInterface $input): array
    {
        if ($input->getOption('all')) {
            return [];
        }

        if ($input->getOption('field-identifiers') !== null) {
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

    private function getDateFromPeriod(): DateTimeImmutable
    {
        $dt = new DateTimeImmutable();

        return $dt->sub($this->period);
    }
}
