<?php

namespace Dew\Cli\Commands;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Yaml\Yaml;

#[AsCommand(name: 'init', description: 'Initialize the current project')]
class InitCommand extends Command
{
    protected function configure(): void
    {
        $this->addArgument('project', InputArgument::OPTIONAL, 'Project ID');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $configFile = getcwd().'/dew.yaml';

        if (file_exists($configFile)) {
            $io->note('Project has been initialized.');

            return Command::FAILURE;
        }

        $yaml = Yaml::dump([
            'id' => (int) ($input->getArgument('project') ?: $io->ask('The project ID')),
        ]);

        file_put_contents($configFile, $yaml);

        $io->success('Project is initialized.');

        return Command::SUCCESS;
    }
}