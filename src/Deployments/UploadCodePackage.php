<?php

namespace Dew\Cli\Deployments;

use Dew\Cli\Deployment;
use Dew\Cli\Oss;

class UploadCodePackage
{
    public function __invoke(Deployment $deployment): Deployment
    {
        echo "Upload the code package\n";

        $project = $deployment->project();
        $oss = Oss::forProject($project);

        $oss->uploadFile($project->deploymentBucket(), $deployment->zipName(), $deployment->zipPath());

        return $deployment;
    }
}