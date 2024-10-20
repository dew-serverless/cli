<?php

namespace Dew\Cli\Deployments;

use Dew\Cli\Deployment;
use Dew\Cli\ExecuteCommand;

class RunDeploySteps
{
    /**
     * Execute the job.
     */
    public function __invoke(Deployment $deployment): Deployment
    {
        $deployment->output?->writeln('Run deploy steps');

        $config = $deployment->config->getEnvironment($deployment->environment);

        $steps = $config['deploy'] ?? [];

        if (empty($steps)) {
            return $deployment;
        }

        $action = (new ExecuteCommand($deployment->client, $deployment->output))
            ->forProject($deployment->config->getId())
            ->on($deployment->environment);

        foreach ($steps as $command) {
            $action->execute($command);
        }

        return $deployment;
    }
}
