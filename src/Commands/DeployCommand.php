<?php

namespace Dew\Cli\Commands;

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
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // The project should be resolved from API endpoint dynamically based
        // on the Dew token, but now in the early stage of development, lets
        // use environment variables from shell instead for fast moving on.
        $project = new Project(getenv('DEW_PROJECT_NAME'), getenv('DEW_PROJECT_REGION'));

        $project->credentialsUsing(new Credentials(
            getenv('ALI_KEY_ID'), getenv('ALI_KEY_SECRET'), getenv('ALI_ACCOUNT_ID')
        ));

        $deployment = new Deployment(getcwd());
        $deployment->projectUsing($project);

        $deployment->handle();

        return Command::SUCCESS;
    }
}