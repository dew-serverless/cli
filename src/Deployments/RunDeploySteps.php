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
        echo "Run deploy steps\n";

        $config = $deployment->config->get($deployment->environment);

        $steps = $config['deploy'] ?? [];

        if (empty($steps)) {
            return $deployment;
        }

        $action = (new ExecuteCommand)
            ->forProject($deployment->config->get('id'))
            ->tokenUsing($deployment->token)
            ->on($deployment->environment);

        foreach ($steps as $command) {
            $response = $action->execute($command);

            echo $response['data']['output'];
        }

        return $deployment;
    }
}
