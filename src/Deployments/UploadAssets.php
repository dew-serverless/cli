<?php

declare(strict_types=1);

namespace Dew\Cli\Deployments;

use Dew\Cli\Deployment;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\Finder\Finder;

class UploadAssets
{
    /**
     * Execute the job.
     */
    public function __invoke(Deployment $deployment): Deployment
    {
        $deployment->output?->writeln('Upload assets');

        $assets = $this->files()->in(
            $publicPath = Path::join($deployment->buildDir(), $deployment->publicPath())
        );

        foreach ($assets as $file) {
            $relativePath = Path::makeRelative($file->getPath(), $publicPath);

            $deployment->output?->writeln(
                Path::join($deployment->context['uuid'], $relativePath, $file->getFilename())
            );
        }

        return $deployment;
    }

    /**
     * Asset files.
     */
    public function files(): Finder
    {
        return (new Finder)->files()->notName('*.php');
    }
}
