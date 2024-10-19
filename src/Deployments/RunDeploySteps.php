<?php

namespace Dew\Cli\Deployments;

use Dew\Cli\Client;
use Dew\Cli\Deployment;
use Dew\Cli\ExecuteCommand;

class RunDeploySteps
{
    /**
     * Execute the job.
     */
    public function __invoke(Deployment $deployment): Deployment
    {
        echo "Run deploy steps\n";

        $config = $deployment->config->getEnvironment($deployment->environment);

        $steps = $config['deploy'] ?? [];

        if (empty($steps)) {
            return $deployment;
        }

        $client = Client::make([
            'token' => $deployment->token,
        ]);

        $action = (new ExecuteCommand($client, $deployment->output))
            ->forProject($deployment->config->getId())
            ->on($deployment->environment);

        foreach ($steps as $command) {
            $action->execute($command);
        }

        return $deployment;
    }
}
