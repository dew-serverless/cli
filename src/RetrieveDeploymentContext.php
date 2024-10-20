<?php

namespace Dew\Cli;

class RetrieveDeploymentContext
{
    /**
     * Retrieve the context for deployment.
     */
    public function __invoke(Deployment $deployment): Deployment
    {
        $response = $deployment->client->createDeployment(
            $deployment->config->getId(), $deployment->environment, [
                'manifest' => $deployment->config->getRaw(),
            ]
        );

        return $deployment->contextUsing($response['data']);
    }
}
