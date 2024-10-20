<?php

namespace Dew\Cli\Deployments;

use Dew\Cli\Deployment;
use Symfony\Component\Filesystem\Filesystem;

class PrepareBuildDirectory
{
    public function __invoke(Deployment $deployment): Deployment
    {
        $deployment->output?->writeln('Prepare build directory');

        $filesystem = new Filesystem;

        if ($filesystem->exists($deployment->buildDir())) {
            $filesystem->remove($deployment->buildDir());
        }

        $filesystem->mkdir($deployment->buildDir());

        return $deployment;
    }
}
