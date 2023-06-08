<?php

namespace Dew\Cli\Commands;

use Dew\Cli\Configuration;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

#[AsCommand(name: 'login', description: 'Authenticate with Dew')]
class LoginCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');

        $question = new Question('Please enter your access token: ');

        $accessToken = $helper->ask($input, $output, $question);

        Configuration::createFromEnvironment()->setToken($accessToken);

        $output->writeln('Login successfully!');

        return Command::SUCCESS;
    }
}