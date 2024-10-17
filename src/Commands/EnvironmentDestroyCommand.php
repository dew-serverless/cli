<?php

namespace Dew\Cli\Commands;

use Dew\Cli\Client;
use Dew\Cli\Configuration;
use Dew\Cli\ProjectConfig;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'environment:destroy', description: 'Destroy an environment')]
class EnvironmentDestroyCommand extends Command
{
    protected function configure(): void
    {
        $this->addArgument('name', InputArgument::OPTIONAL, 'Environment name');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $token = Configuration::createFromEnvironment()->getToken();
        $projectConfig = ProjectConfig::load();
        $io = new SymfonyStyle($input, $output);
        $environment = $input->getArgument('name') ?: $io->ask('Environment name');

        $response = Client::make()->delete(sprintf(
            '/api/projects/%s/environments/%s',
            $projectConfig->get('id'), $environment
        ));

        if ($response->getStatusCode() >= 400) {
            $io->error('Could not destroy the environment.');

            return Command::FAILURE;
        }

        $io->success('Environment successfully destroyed!');

        return Command::SUCCESS;
    }
}
