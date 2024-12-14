<?php

declare(strict_types=1);

namespace Dew\Cli;

class RetrieveDeploymentContext
{
    /**
     * Retrieve the context for deployment.
     */
    public function __invoke(Deployment $deployment): Deployment
    {
        $response = $deployment->client->createDeployment(
            $deployment->config->getId(), [
                'manifest' => $deployment->config->getRaw(),
                'production' => $deployment->isProduction,
                'php' => PhpVersion::fromComposer(implode(DIRECTORY_SEPARATOR, [
                    $deployment->appDir(),
                    'composer.json',
                ])),
            ]
        );

        return $deployment->contextUsing($response['data']);
    }
}
