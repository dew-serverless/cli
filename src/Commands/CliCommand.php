<?php

declare(strict_types=1);

namespace Dew\Cli\Commands;

use Dew\Cli\Client;
use Dew\Cli\ExecuteCommand;
use Dew\Cli\ProjectConfig;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'cli', description: 'Execute command')]
class CliCommand extends Command
{
    /**
     * Configures the current command.
     */
    protected function configure(): void
    {
        $this->addArgument('env', InputArgument::REQUIRED, 'The project environment');
        $this->addArgument('cmd', InputArgument::REQUIRED, 'The command to be executed');
    }

    /**
     * Executes the current command.
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        return (new ExecuteCommand(Client::make(), $output))
            ->forProject(ProjectConfig::load()->getId())
            ->on($input->getArgument('env'))
            ->execute($input->getArgument('cmd'));
    }
}
