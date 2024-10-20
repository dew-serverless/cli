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

#[AsCommand(name: 'rollback', description: 'Rollback deployment')]
class RollbackCommand extends Command
{
    protected function configure(): void
    {
        $this->addArgument('environment', InputArgument::OPTIONAL, 'Environment name');
        $this->addArgument('deployment', InputArgument::OPTIONAL, 'Deployment ID');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $config = ProjectConfig::load();

        $response = Client::make()
            ->post(sprintf('/api/projects/%s/environments/%s/deployments/%s/rollback',
                $config->getId(),
                $input->getArgument('environment') ?: $io->ask('Environment name'),
                $input->getArgument('deployment') ?: $io->ask('Deployment ID')
            ));

        if ($response->getStatusCode() >= 400) {
            $io->error('Failed to rollback the deployment.');

            return Command::FAILURE;
        }

        $io->success('Deployment rollback successfully!');

        return Command::SUCCESS;
    }
}
