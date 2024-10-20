<?php

declare(strict_types=1);

namespace Dew\Cli\Deployments;

use Dew\Cli\Deployment;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\Finder\Finder;
use ZipArchive;

class PackageUpBuildDirectory
{
    public function __invoke(Deployment $deployment): Deployment
    {
        $deployment->output?->writeln('Archive source code');

        $files = $this->files()->in($deployment->buildDir());

        $zip = new ZipArchive;

        $zip->open(Path::join($deployment->zipPath()), ZipArchive::CREATE);

        foreach ($files as $file) {
            $entryName = Path::makeRelative($file->getRealPath(), $deployment->buildDir());

            if ($file->isDir()) {
                $zip->addEmptyDir($entryName);
            } else {
                $zip->addFile($file->getRealPath(), $entryName);
            }
        }

        $zip->close();

        return $deployment;
    }

    public function files(): Finder
    {
        return (new Finder)->ignoreDotFiles(false);
    }
}
