<?php

declare(strict_types=1);

namespace Dew\Cli\Deployments;

use Dew\Cli\Deployment;

class UploadCodePackage
{
    public function __invoke(Deployment $deployment): Deployment
    {
        $deployment->output?->writeln('Upload code package');

        $deployment->output?->writeln($deployment->zipPath());

        return $deployment;
    }
}
