<?php

declare(strict_types=1);

namespace Dew\Cli\Deployments;

use Dew\Cli\Deployment;
use Dew\Cli\Git;
use Dew\Cli\PhpVersion;

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
                ...$this->gitContext($deployment->appDir()),
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

    /**
     * @return array<string, mixed>
     */
    public function gitContext(string $path): array
    {
        $git = Git::fromContext($path);

        if (! $git instanceof Git) {
            return [];
        }

        return [
            'git' => [
                'commit_hash' => $git->hash,
                'commit_message' => $git->subject,
                'author_name' => $git->authorName,
                'author_email' => $git->authorEmail,
                'branch' => $git->branch,
                'is_dirty' => $git->isDirty,
            ],
        ];
    }
}
