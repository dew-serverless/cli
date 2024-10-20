<?php

declare(strict_types=1);

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

        $filesystem->copy($stubs.'/runtime.php', $buildDir.'/runtime.php');
        $filesystem->copy($stubs.'/fpmRuntime.php', $buildDir.'/fpmRuntime.php');
        $filesystem->copy($stubs.'/cliRuntime.php', $buildDir.'/cliRuntime.php');
        $filesystem->copy($stubs.'/handler.php', $buildDir.'/handler.php');

        return $deployment;
    }
}
