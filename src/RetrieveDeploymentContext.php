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
                'php' => $this->phpVersion($deployment),
            ]
        );

        return $deployment->contextUsing($response['data']);
    }

    public function phpVersion(Deployment $deployment): ?string
    {
        return $this->phpVersionFromComposerJson($deployment)
            ?? $this->phpVersionFromRuntime()
            ?? null;
    }

    public function phpVersionFromComposerJson(Deployment $deployment): ?string
    {
        return PhpVersion::fromComposer(implode(DIRECTORY_SEPARATOR, [
            $deployment->appDir(), 'composer.json'
        ]));
    }

    public function phpVersionFromRuntime(): ?string
    {
        return PhpVersion::fromRuntime();
    }
}
