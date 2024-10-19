<?php

namespace Dew\Cli\Deployments;

use Dew\Cli\Client;
use Dew\Cli\Deployment;

class ReleaseVersion
{
    public function __invoke(Deployment $deployment): Deployment
    {
        echo "Release a new version\n";

        Client::make(['token' => $deployment->token])
            ->post($deployment->context['callback']);

        return $deployment;
    }
}
