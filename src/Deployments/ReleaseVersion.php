<?php

namespace Dew\Cli\Deployments;

use Dew\Cli\Client;
use Dew\Cli\Deployment;

class ReleaseVersion
{
    public function __invoke(Deployment $deployment): Deployment
    {
        echo "Release a new version\n";

        Client::make()->post($deployment->context['callback'], [
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => sprintf('Bearer %s', $deployment->token),
            ],
        ]);

        return $deployment;
    }
}