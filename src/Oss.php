<?php

namespace Dew\Cli;

use OSS\OssClient;

class Oss
{
    public static function forProject(Project $project)
    {
        $credentials = $project->credentials();

        $endpoint = sprintf('oss-%s.aliyuncs.com', $project->region());

        return new OssClient($credentials->keyId(), $credentials->keySecret(), $endpoint);
    }
}