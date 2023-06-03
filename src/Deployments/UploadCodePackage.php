<?php

namespace Dew\Cli\Deployments;

use Dew\Cli\Deployment;

class UploadCodePackage
{
    public function __invoke(Deployment $deployment): Deployment
    {
        echo "Upload the code package\n";

        return $deployment;
    }
}