<?php

namespace Dew\Cli\Commands;

use Dew\Cli\Configuration;
use Dew\Cli\Database\CreateDatabaseInstanceHandler;
use Dew\Cli\ProjectConfig;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'db:create', description: 'Create a database instance', aliases: ['db'])]
class DatabaseCreateCommand extends Command
{
    /**
     * Configures the current command.
     */
    protected function configure(): void
    {
        $this->addArgument('name', InputArgument::OPTIONAL, 'Instance name');
        $this->addArgument('engine', InputArgument::OPTIONAL, 'Database engine');
        $this->addArgument('type', InputArgument::OPTIONAL, 'Instance type');
    }

    /**
     * Executes the current command.
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        (new CreateDatabaseInstanceHandler($input, new SymfonyStyle($input, $output)))
            ->tokenUsing(Configuration::createFromEnvironment()->getToken())
            ->forProject(ProjectConfig::load()->get('id'))
            ->handle();

        return Command::SUCCESS;
    }
}