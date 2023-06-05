<?php

namespace Dew\Cli\Deployments;

use Dew\Cli\Deployment;
use Symfony\Component\Filesystem\Filesystem;

class PublishStubs
{
    public function __invoke(Deployment $deployment): Deployment
    {
        $filesystem = new Filesystem;

        $buildDir = $deployment->buildDir();
        $stubs = $buildDir.'/vendor/dew/core/stubs';

        $filesystem->copy($stubs.'/.rr.yaml', $buildDir.'/.rr.yaml');
        $filesystem->copy($stubs.'/runtime.php', $buildDir.'/runtime.php');
        $filesystem->copy($stubs.'/handler.php', $buildDir.'/handler.php');

        return $deployment;
    }
}