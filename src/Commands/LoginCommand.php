<?php

namespace Dew\Cli\Commands;

use Dew\Cli\Configuration;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'login', description: 'Authenticate with Dew')]
class LoginCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $accessToken = $io->ask('Access token of Dew');

        Configuration::createFromEnvironment()->setToken($accessToken);

        $io->success('Login successfully!');

        return Command::SUCCESS;
    }
}
