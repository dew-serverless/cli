<?php

namespace Dew\Cli\Deployments;

use Dew\Cli\Deployment;
use OSS\OssClient;

class UploadCodePackage
{
    public function __invoke(Deployment $deployment): Deployment
    {
        echo "Upload the code package\n";

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