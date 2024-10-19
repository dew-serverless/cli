<?php

namespace Dew\Cli\Deployments;

use Dew\Cli\Deployment;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\Finder\Finder;

class CopyFilesToBuildDirectory
{
    public function __invoke(Deployment $deployment): Deployment
    {
        echo "Copying files to build directory\n";

        $filesystem = new Filesystem;
        $files = $this->files()->in($deployment->appDir());

        foreach ($files as $file) {
            $relativePath = Path::makeRelative($file->getRealPath(), $deployment->appDir());
            $targetPath = Path::join($deployment->buildDir(), $relativePath);

            if ($file->isDir()) {
                $filesystem->mkdir($targetPath, $file->getPerms());
            } else {
                $filesystem->copy($file->getRealPath(), $targetPath);
            }
        }

        return $deployment;
    }

    protected function files(): Finder
    {
        return (new Finder)
            ->ignoreDotFiles(true)
            ->ignoreVCS(true)
            ->ignoreVCSIgnored(true)
            ->ignoreUnreadableDirs();
    }
}
