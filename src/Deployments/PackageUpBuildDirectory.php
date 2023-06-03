<?php

namespace Dew\Cli\Deployments;

use Dew\Cli\Deployment;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\Finder\Finder;
use ZipArchive;

class PackageUpBuildDirectory
{
    public function __invoke(Deployment $deployment): Deployment
    {
        echo "Package up build directory\n";

        $files = (new Finder)->in($deployment->buildDir());

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
}