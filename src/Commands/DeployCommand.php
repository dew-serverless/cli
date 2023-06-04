<?php

namespace Dew\Cli\Commands;

use Dew\Cli\Concerns\ResolvesProject;
use Dew\Cli\Credentials;
use Dew\Cli\Deployment;
use Dew\Cli\Project;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'deploy', description: 'Deploy the app to Alibaba Cloud')]
class DeployCommand extends Command
{
    use ResolvesProject;

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $deployment = new Deployment(getcwd());
        $deployment->projectUsing($this->project());

        $deployment->handle();

        return Command::SUCCESS;
    }
}