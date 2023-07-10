<?php

namespace Dew\Cli;

use Dew\Cli\Deployments\CopyFilesToBuildDirectory;
use Dew\Cli\Deployments\InstallDependencies;
use Dew\Cli\Deployments\PackageUpBuildDirectory;
use Dew\Cli\Deployments\PrepareBuildDirectory;
use Dew\Cli\Deployments\PublishStubs;
use Dew\Cli\Deployments\ReleaseVersion;
use Dew\Cli\Deployments\UploadAssets;
use Dew\Cli\Deployments\UploadCodePackage;
use Symfony\Component\Filesystem\Path;

class Deployment
{
    /**
     * Process of deployment.
     */
    protected const PROCESS = [
        RetrieveDeploymentContext::class,
        PrepareBuildDirectory::class,
        CopyFilesToBuildDirectory::class,
        UploadAssets::class,
        InstallDependencies::class,
        PublishStubs::class,
        PackageUpBuildDirectory::class,
        UploadCodePackage::class,
        ReleaseVersion::class,
    ];

    /**
     * Context of the deployment.
     */
    public array $context;

    /**
     * Token for communicating with Dew.
     */
    public string $token;

    /**
     * Name of the environment.
     */
    public string $environment;

    /**
     * Project configuration.
     */
    public ProjectConfig $config;

    /**
     * The public path of the application.
     */
    protected string $publicPath;

    public function __construct(
        protected string $appDir
    ) {
        //
    }

    /**
     * Configure Dew token.
     */
    public function tokenUsing(string $token): self
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Configure project configuration.
     */
    public function configUsing(ProjectConfig $config): self
    {
        $this->config = $config;

        return $this;
    }

    /**
     * Configure environment to deploy.
     */
    public function forEnvironment(string $environment): self
    {
        $this->environment = $environment;

        return $this;
    }

    /**
     * Configure deployment context.
     */
    public function contextUsing(array $context): self
    {
        $this->context = $context;

        return $this;
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

    /**
     * Configure public path of the application.
     */
    public function usePublicPath(string $path): self
    {
        $this->publicPath = $path;

        return $this;
    }

    /**
     * The public path of the application.
     */
    public function publicPath(): string
    {
        return $this->publicPath;
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