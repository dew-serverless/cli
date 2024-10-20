<?php

declare(strict_types=1);

namespace Dew\Cli\Commands;

use Dew\Cli\Client;
use Dew\Cli\ProjectConfig;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'environment:create', description: 'Create a new environment')]
class EnvironmentCreateCommand extends Command
{
    protected function configure(): void
    {
        $this->addArgument('name', InputArgument::OPTIONAL, 'Environment name');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $projectConfig = ProjectConfig::load();
        $io = new SymfonyStyle($input, $output);

        $response = Client::make()
            ->post(sprintf('/api/projects/%s/environments', $projectConfig->getId()), [
                'name' => $input->getArgument('name') ?: $io->ask('Environment name'),
            ]);

        if ($response->getStatusCode() >= 400) {
            $io->error('Could not create environment.');

            return Command::FAILURE;
        }

        $io->success('Environment successfully created!');

        return Command::SUCCESS;
    }
}
