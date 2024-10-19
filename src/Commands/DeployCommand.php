<?php

namespace Dew\Cli\Commands;

use Dew\Cli\Configuration;
use Dew\Cli\Deployment;
use Dew\Cli\ProjectConfig;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Throwable;

#[AsCommand(name: 'deploy', description: 'Deploy the app to Alibaba Cloud')]
class DeployCommand extends Command
{
    protected function configure(): void
    {
        $this->addArgument('environment', InputArgument::OPTIONAL, 'Environment name');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $deployment = (new Deployment(getcwd()))
            ->usePublicPath('/public')
            ->tokenUsing(Configuration::createFromEnvironment()->getToken())
            ->configUsing(ProjectConfig::load())
            ->forEnvironment($input->getArgument('environment') ?: $io->ask('Environment name'))
            ->outputUsing($output);

        try {
            $deployment->handle();
        } catch (Throwable $e) {
            $io->error(sprintf('Failed to make a deployment: %s', $e->getMessage()));

            return Command::FAILURE;
        }

        $io->success('Deploy successfully!');

        return Command::SUCCESS;
    }
}
