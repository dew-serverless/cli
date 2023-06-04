<?php

namespace Dew\Cli;

use Dew\Cli\Deployments\CopyFilesToBuildDirectory;
use Dew\Cli\Deployments\InstallDependencies;
use Dew\Cli\Deployments\PackageUpBuildDirectory;
use Dew\Cli\Deployments\PrepareBuildDirectory;
use Dew\Cli\Deployments\PublishStubs;
use Dew\Cli\Deployments\ReleaseVersion;
use Dew\Cli\Deployments\UploadCodePackage;
use Symfony\Component\Filesystem\Path;

class Deployment
{
    protected const PROCESS = [
        PrepareBuildDirectory::class,
        CopyFilesToBuildDirectory::class,
        InstallDependencies::class,
        PublishStubs::class,
        PackageUpBuildDirectory::class,
        UploadCodePackage::class,
        ReleaseVersion::class,
    ];

    /**
     * The project associated with the deployment.
     *
     * @var Project
     */
    protected Project $project;

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

    /**
     * Associate the deployment with given project.
     *
     * @param  Project  $project
     * @return $this
     */
    public function projectUsing(Project $project): self
    {
        $this->project = $project;

        return $this;
    }

    /**
     * Retrieve the associated project.
     *
     * @return Project
     */
    public function project(): Project
    {
        return $this->project;
    }

    public function appDir()
    {
        return $this->appDir;
    }

    public function buildDir()
    {
        return Path::join($this->appDir(), '.dew', 'build');
    }

    public function zipName()
    {
        return 'build.zip';
    }

    public function zipPath()
    {
        return Path::join($this->appDir(), '.dew', $this->zipName());
    }
}