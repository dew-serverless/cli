<?php

namespace Dew\Cli\Deployments;

use AlibabaCloud\SDK\FCOpen\V20210406\FCOpen;
use AlibabaCloud\SDK\FCOpen\V20210406\Models\Code;
use AlibabaCloud\SDK\FCOpen\V20210406\Models\UpdateFunctionRequest;
use Darabonba\OpenApi\Models\Config;
use Dew\Cli\Deployment;

class ReleaseVersion
{
    const HTTP_FUNCTION = 'http';
    const CONSOLE_FUNCTION = 'console';

    public function __invoke(Deployment $deployment): Deployment
    {
        echo "Release a new version\n";

        $project = $deployment->project();
        $credentials = $project->credentials();

        $fc = new FcOpen(new Config([
            'accessKeyId' => $credentials->keyId(),
            'accessKeySecret' => $credentials->keySecret(),
            'endpoint' => sprintf('%s.%s.fc.aliyuncs.com', $credentials->accountId(), $project->region()),
        ]));

        $request = new UpdateFunctionRequest([
            'code' => new Code([
                'ossBucketName' => $project->deploymentBucket(),
                'ossObjectName' => $deployment->zipName(),
            ]),
        ]);

        $fc->updateFunction($project->serviceName(), self::HTTP_FUNCTION, $request);
        $fc->updateFunction($project->serviceName(), self::CONSOLE_FUNCTION, $request);

        return $deployment;
    }
}