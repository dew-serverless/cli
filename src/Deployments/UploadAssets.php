<?php

namespace Dew\Cli\Deployments;

use Dew\Cli\Deployment;
use OSS\OssClient;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\Finder\Finder;

class UploadAssets
{
    /**
     * Execute the job.
     */
    public function __invoke(Deployment $deployment): Deployment
    {
        echo "Upload the assets\n";

        $oss = new OssClient(
            $deployment->context['credentials']['access_key_id'],
            $deployment->context['credentials']['access_key_secret'],
            sprintf('oss-%s.aliyuncs.com', $deployment->context['region']),
            $isCname = false,
            $deployment->context['credentials']['security_token'],
        );

        $assets = $this->files()->in(
            $publicPath = Path::join($deployment->buildDir(), $deployment->publicPath())
        );

        foreach ($assets as $file) {
            $relativePath = Path::makeRelative($file->getPath(), $publicPath);

            $oss->uploadFile(
                $deployment->context['asset_bucket'],
                Path::join($deployment->context['uuid'], $relativePath, $file->getFilename()),
                $file
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