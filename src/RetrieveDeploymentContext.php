<?php

namespace Dew\Cli;

class RetrieveDeploymentContext
{
    /**
     * Retrieve the context for deployment.
     */
    public function __invoke(Deployment $deployment): Deployment
    {
        $environment = $deployment->config->get($deployment->environment);

        $response = Client::make()->post(sprintf(
            '/api/projects/%s/environments/%s/deployments',
            $deployment->config->get('id'), $deployment->environment
        ), [
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => sprintf('Bearer %s', $deployment->token),
            ],
            'json' => [
                'cpu' => $environment['cpu'],
                'ram' => $environment['ram'],
                'php' => $environment['php'],
                'env' => $environment['env'] ?? [],
                'layers' => $environment['layers'] ?? [],
                'database' => $environment['database'] ?? null,
                'cache' => $environment['cache'] ?? null,
                'queue' => $environment['queue'] ?? null,
            ],
        ]);

        $data = json_decode($response->getBody()->getContents(), associative: true);

        $deployment->contextUsing($data['data']);

        return $deployment;
    }
}
