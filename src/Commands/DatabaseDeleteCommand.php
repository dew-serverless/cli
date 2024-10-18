<?php

namespace Dew\Cli\Commands;

use Dew\Cli\Configuration;
use Dew\Cli\Database\DeleteDatabaseInstance;
use Dew\Cli\ProjectConfig;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'db:delete', description: 'Delete a database instance')]
class DatabaseDeleteCommand extends Command
{
    /**
     * Configures the current command.
     */
    protected function configure(): void
    {
        $this->addArgument('name', InputArgument::OPTIONAL, 'The name of database instance');
    }

    /**
     * Executes the current command.
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $action = (new DeleteDatabaseInstance)
            ->tokenUsing(Configuration::createFromEnvironment()->getToken())
            ->forProject(ProjectConfig::load()->getId())
            ->forInstance($input->getArgument('name') ?: $io->ask('What is the name of the database instance'));

        if ($io->confirm('Do you want to delete the database instance')) {
            $action->delete();

            $io->success('The database instance has been deleted!');
        }

        return Command::SUCCESS;
    }
}
