<?php

namespace Dew\Cli;

class RetrieveDeploymentContext
{
    /**
     * Retrieve the context for deployment.
     */
    public function __invoke(Deployment $deployment): Deployment
    {
        $response = Client::make(['token' => $deployment->token])
            ->post(sprintf(
                '/api/projects/%s/environments/%s/deployments',
                $deployment->config->getId(), $deployment->environment
            ), [
                'manifest' => $deployment->config->getRaw(),
            ]);

        $deployment->contextUsing($response['data']);

        return $deployment;
    }
}
