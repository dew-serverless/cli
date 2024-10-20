<?php

declare(strict_types=1);

namespace Dew\Cli\Deployments;

use Dew\Cli\Deployment;
use OSS\OssClient;

class UploadCodePackage
{
    public function __invoke(Deployment $deployment): Deployment
    {
        $deployment->output?->writeln('Upload code package');

        $oss = new OssClient(
            $deployment->context['credentials']['access_key_id'],
            $deployment->context['credentials']['access_key_secret'],
            sprintf('oss-%s.aliyuncs.com', $deployment->context['region']),
            $isCname = false,
            $deployment->context['credentials']['security_token'],
        );

        $oss->uploadFile(
            $deployment->context['deployment_bucket'],
            $deployment->context['deployment_object'],
            $deployment->zipPath()
        );

        return $deployment;
    }
}
