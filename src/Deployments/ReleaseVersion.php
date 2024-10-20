<?php

declare(strict_types=1);

namespace Dew\Cli\Deployments;

use Dew\Cli\Deployment;

class ReleaseVersion
{
    public function __invoke(Deployment $deployment): Deployment
    {
        $deployment->output?->writeln('Release a new version');

        $deployment->client->pingDeploymentCallback(
            $deployment->context['callback']
        );

        return $deployment;
    }
}
