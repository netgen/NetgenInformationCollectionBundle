<?php

namespace Netgen\Bundle\InformationCollectionBundle\Command;

use DateTimeImmutable;
use Netgen\InformationCollection\Core\Persistence\Anonymizer\AnonymizerServiceFacade;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\HelpCommand;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use DateInterval;
use Exception;

class DataAnonymizerCommand extends Command
{
    protected static $defaultName = 'nginfocollector:anonymize';

    /**
     * @var \Netgen\InformationCollection\Core\Persistence\Anonymizer\AnonymizerServiceFacade
     */
    protected $anonymizer;

    /**
     * @var \DateInterval
     */
    protected $period;

    public function __construct(AnonymizerServiceFacade $anonymizerServiceFacade)
    {
        $this->anonymizer = $anonymizerServiceFacade;

        // Parent constructor call is mandatory for commands registered as services
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName("nginfocollector:anonymize");
        $this->setDescription("Anonymizes collected data in collected info tables.");
        $this->setHelp("This command allows you to anonymize data collected by this library in collected info tables.");

        $this->setDefinition(
            new InputDefinition(
                [
                    new InputOption('content-id', 'c', InputOption::VALUE_REQUIRED, "Content id."),
                    new InputOption('field-identifiers', 'f', InputOption::VALUE_REQUIRED, "Field definition identifiers list."),
                    new InputOption('period', 'p', InputOption::VALUE_REQUIRED, "Attributes older that this period will be anonymized."),
                    new InputOption('all', 'a', InputOption::VALUE_NONE, "Anonymize all fields."),
                    new InputOption('neglect', 'nn', InputOption::VALUE_NONE, "Do not ask for confirmation."),
                ]
            )
        );

        $this->addUsage("--content-id=123 --field-identifiers=title,name,last_name");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (is_null($input->getOption('content-id'))) {
            $output->writeln("<error>                                       </error>");
            $output->writeln("<error>     Missing content-id parameter.     </error>");
            $output->writeln("<error>                                       </error>");

            return $this->displayHelp($input, $output);
        }

        if (is_null($input->getOption('field-identifiers')) && !$input->getOption('all')) {
            $output->writeln("<error>                                              </error>");
            $output->writeln("<error>     Missing field-identifiers parameter.     </error>");
            $output->writeln("<error>                                              </error>");

            return $this->displayHelp($input, $output);
        }

        $contentId = intval($input->getOption('content-id'));
        $fields = $this->getFields($input);

        $info = sprintf("Command will anonymize <info>%s</info> fields for content #%d", empty($fields) ? 'all': implode(", ", $fields), $contentId);
        $output->writeln($info);

        if ($this->proceedWithAction($input, $output)) {
            $output->write("<info>Running.... </info>");
            $count = $this->anonymizer->anonymize($contentId, $fields, $this->getDateFromPeriod());
            $output->writeln("<info>Done.</info>");
            $output->writeln("<info>Anonymized #{$count} collections.</info>");
            return 0;
        }

        $output->writeln("<info>Canceled.</info>");
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
                $output->writeln("Please enter valid DateInterval string.");
                exit(0);
            }
        }
    }

    protected function displayHelp(InputInterface $input, OutputInterface $output)
    {
        $help = new HelpCommand();
        $help->setCommand($this);

        return $help->run($input, $output);
    }

    protected function getFields(InputInterface $input)
    {
        if (!empty($input->getOption('all'))) {
            return [];
        }

        if (!is_null($input->getOption('field-identifiers'))) {

            $ids = [];
            $fieldIdentifiers = $input->getOption('field-identifiers');

            if (is_string($fieldIdentifiers)) {
                $ids = explode(",", $fieldIdentifiers);
            }

            if (is_array($fieldIdentifiers)) {
                $ids = array_filter($fieldIdentifiers);
            }

            return array_unique((array)$ids);
        }

        return [];
    }

    protected function proceedWithAction(InputInterface $input, OutputInterface $output)
    {
        if ($input->getOption('neglect')) {
            return true;
        }
        $helper = $this->getHelper('question');
        $question = new ConfirmationQuestion("Continue with this action? y/n ", false, '/^(y|j)/i');

        if ($helper->ask($input, $output, $question)) {
            return true;
        }

        return false;
    }

    protected function getDateFromPeriod(): DateTimeImmutable
    {
        $dt = new DateTimeImmutable();

        return $dt->sub($this->period);
    }
}
