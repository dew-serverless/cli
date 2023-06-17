<?php

namespace Dew\Cli\Commands;

use Dew\Cli\Client;
use Dew\Cli\Configuration;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'project:create', description: 'Create a new project')]
class ProjectCreateCommand extends Command
{
    protected function configure(): void
    {
        $this->addArgument('name', InputArgument::OPTIONAL, 'Project name');
        $this->addArgument('region', InputArgument::OPTIONAL, 'Project region');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $token = Configuration::createFromEnvironment()->getToken();

        $io = new SymfonyStyle($input, $output);

        $regions = ['us-west-1'];

        $name = $input->getArgument('name') ?: $io->ask('The project name');

        $region = $input->getArgument('region')
            ?: $io->choice('The project deployed to', $regions, $regions[0]);

        $response = Client::make()->post('/api/projects', [
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => sprintf('Bearer %s', $token),
            ],
            'json' => [
                'name' => $name,
                'region' => $region,
            ],
        ]);

        if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
            $io->success('Project created!');

            return Command::SUCCESS;
        }

        $io->error('Failed to create project.');

        return Command::FAILURE;
    }
}