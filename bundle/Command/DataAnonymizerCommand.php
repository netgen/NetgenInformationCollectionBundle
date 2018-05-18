<?php

namespace Netgen\Bundle\InformationCollectionBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\HelpCommand;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class DataAnonymizerCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName("netgen:collected-info:anonymize");
        $this->setDescription("Anonymizes collected data in collected info tables.");
        $this->setHelp("This command allows you to anonymize data collected by this library in collected info tables.");

        $this->setDefinition(
            new InputDefinition(
                [
                    new InputOption('content-id', 'c', InputOption::VALUE_REQUIRED, "Content id."),
                    new InputOption('info-collection-id', 'i', InputOption::VALUE_REQUIRED, "Info collection id from database."),
                    new InputOption('field-identifiers', 'f', InputOption::VALUE_REQUIRED, "Field definition identifiers list."),
                    new InputOption('all', 'a', InputOption::VALUE_NONE, "Anonymize all fields."),
                    new InputOption('neglect', 'nn', InputOption::VALUE_NONE, "Do not ask for confirmation."),
                ]
            )
        );

        $this->addUsage("--content-id=123 --field-identifiers=title,name,last_name");
        $this->addUsage("--info-collection-id=456 --field-identifiers=title,name,last_name");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (is_null($input->getOption('content-id')) && is_null($input->getOption('info-collection-id'))) {
            $output->writeln("<error>                                                             </error>");
            $output->writeln("<error>     Missing content-id or info-collection-id parameter.     </error>");
            $output->writeln("<error>                                                             </error>");

            return $this->displayHelp($input, $output);
        }

        if (is_null($input->getOption('field-identifiers')) && !$input->getOption('all')) {
            $output->writeln("<error>                                              </error>");
            $output->writeln("<error>     Missing field-identifiers parameter.     </error>");
            $output->writeln("<error>                                              </error>");

            return $this->displayHelp($input, $output);
        }

        if (!is_null($input->getOption('content-id'))) {
            return $this->handleByContent($input, $output);
        }

        if (!is_null($input->getOption('info-collection-id'))) {
            return $this->handleByCollectedInfo($input, $output);
        }
    }

    protected function displayHelp(InputInterface $input, OutputInterface $output)
    {
        $help = new HelpCommand();
        $help->setCommand($this);

        return $help->run($input, $output);
    }

    protected function handleByContent(InputInterface $input, OutputInterface $output)
    {
        $contentId = intval($input->getOption('content-id'));

        if ($input->getOption('all')) {

            $output->writeln("<info>Command will clear all fields for content #{$contentId}</info>");

            if (!$input->getOption('neglect')) {
                $helper = $this->getHelper('question');
                $question = new ConfirmationQuestion("Continue with this action? y/n ", false, '/^(y|j)/i');

                if ($helper->ask($input, $output, $question)) {
                    $output->write("<info>Running.... </info>");
                    $output->writeln("<info>Done</info>");
                    return;
                }

            } else {

            }
        }


        $fields = $this->getFields($input);

        // do something

    }

    protected function handleByCollectedInfo(InputInterface $input, OutputInterface $output)
    {

    }

    protected function getFields(InputInterface $input)
    {
        if (!is_null($input->getOption('field-identifiers'))) {

            $ids = explode(",", $input->getOption('field-identifiers'));
            $ids = array_filter($ids);

            return array_unique($ids);
        }
    }
}