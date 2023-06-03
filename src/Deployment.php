<?php

namespace Dew\Cli;

use Dew\Cli\Deployments\CopyFilesToBuildDirectory;
use Dew\Cli\Deployments\InstallDependencies;
use Dew\Cli\Deployments\PackageUpBuildDirectory;
use Dew\Cli\Deployments\PrepareBuildDirectory;
use Dew\Cli\Deployments\ReleaseVersion;
use Dew\Cli\Deployments\UploadCodePackage;
use Symfony\Component\Filesystem\Path;

class Deployment
{
    protected const PROCESS = [
        PrepareBuildDirectory::class,
        CopyFilesToBuildDirectory::class,
        InstallDependencies::class,
        PackageUpBuildDirectory::class,
        UploadCodePackage::class,
        ReleaseVersion::class,
    ];

    public function __construct(
        protected string $appDir
    ) {
        //
    }

    public function handle(): void
    {
        array_reduce(static::PROCESS, function ($deployment, $step) {
            $handle = new $step;

            return $handle($deployment);
        }, $this);
    }

    public function appDir()
    {
        return $this->appDir;
    }

    public function buildDir()
    {
        return Path::join($this->appDir(), '.dew', 'build');
    }

    public function zipPath()
    {
        return Path::join($this->appDir(), '.dew', 'build.zip');
    }
}