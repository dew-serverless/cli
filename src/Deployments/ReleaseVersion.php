<?php

namespace Dew\Cli\Deployments;

use Dew\Cli\Deployment;

class ReleaseVersion
{
    public function __invoke(Deployment $deployment): Deployment
    {
        echo "Release a new version\n";

        return $deployment;
    }
}