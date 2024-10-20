<?php

declare(strict_types=1);

namespace Dew\Cli;

use Dew\Cli\Contracts\CommunicatesWithDew;
use Dew\Cli\Deployments\CopyFilesToBuildDirectory;
use Dew\Cli\Deployments\PackageUpBuildDirectory;
use Dew\Cli\Deployments\PrepareBuildDirectory;
use Dew\Cli\Deployments\PublishStubs;
use Dew\Cli\Deployments\ReleaseVersion;
use Dew\Cli\Deployments\RunBuildSteps;
use Dew\Cli\Deployments\RunDeploySteps;
use Dew\Cli\Deployments\UploadAssets;
use Dew\Cli\Deployments\UploadCodePackage;
use Symfony\Component\Console\Output\OutputInterface;
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
        RunBuildSteps::class,
        UploadAssets::class,
        PublishStubs::class,
        PackageUpBuildDirectory::class,
        UploadCodePackage::class,
        ReleaseVersion::class,
        RunDeploySteps::class,
    ];

    public CommunicatesWithDew $client;

    /**
     * Context of the deployment.
     *
     * @var array<string, mixed>
     */
    public array $context;

    /**
     * Name of the environment.
     */
    public string $environment;

    public ?OutputInterface $output = null;

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

    public function clientUsing(CommunicatesWithDew $client): self
    {
        $this->client = $client;

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
     *
     * @param  array<string, mixed>  $context
     */
    public function contextUsing(array $context): self
    {
        $this->context = $context;

        return $this;
    }

    public function outputUsing(OutputInterface $output): self
    {
        $this->output = $output;

        return $this;
    }

    public function handle(): void
    {
        array_reduce(static::PROCESS, function ($deployment, $step) {
            $handle = new $step;

            return $handle($deployment);
        }, $this);
    }

    public function appDir(): string
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

    public function buildDir(): string
    {
        return Path::join($this->appDir(), '.dew', 'build');
    }

    public function zipName(): string
    {
        return 'build.zip';
    }

    public function zipPath(): string
    {
        return Path::join($this->appDir(), '.dew', $this->zipName());
    }
}
