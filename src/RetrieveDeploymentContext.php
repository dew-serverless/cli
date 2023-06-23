<?php

namespace Dew\Cli;

class RetrieveDeploymentContext
{
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
            ],
        ]);

        $data = json_decode($response->getBody()->getContents(), associative: true);

        $deployment->contextUsing($data['data']);

        return $deployment;
    }
}