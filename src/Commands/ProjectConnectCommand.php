<?php

declare(strict_types=1);

namespace Dew\Cli\Commands;

use Dew\Cli\Dew;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'project:connect', description: 'Connect project with Alibaba Cloud')]
class ProjectConnectCommand extends Command
{
    protected function configure(): void
    {
        $this->addArgument('project', InputArgument::REQUIRED, 'Project id');
        $this->addArgument('key', InputArgument::OPTIONAL, 'ACS access key id');
        $this->addArgument('secret', InputArgument::OPTIONAL, 'ACS access key secret');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $key = $input->getArgument('key') ?: $io->ask('ACS access key id');
        $secret = $input->getArgument('secret') ?: $io->askHidden('ACS access key secret');

        $response = Dew::make()
            ->post(sprintf('/api/projects/%s/connect', $input->getArgument('project')), [
                'access_key_id' => $key,
                'access_key_secret' => $secret,
            ]);

        if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
            $io->success('Successfully connected with ACS.');

            return Command::SUCCESS;
        }

        $io->error('Failed to connect with ACS.');

        return Command::FAILURE;
    }
}
