<?php

namespace Dew\Cli\Deployments;

use Dew\Cli\Deployment;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Process;

class InstallDependencies
{
    public function __invoke(Deployment $deployment): Deployment
    {
        echo "Install dependencies\n";

        $finder = new ExecutableFinder;
        $composerPath = $finder->find('composer');

        $process = new Process([
            $composerPath, 'install', '--optimize-autoloader', '--no-dev',
        ], $deployment->buildDir());

        $process->run(function ($type, $buffer) {
            echo $buffer;
        });

        return $deployment;
    }
}