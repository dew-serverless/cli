<?php

declare(strict_types=1);

namespace Dew\Cli\Deployments;

use Dew\Cli\Deployment;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7;

class UploadCodePackage
{
    public function __invoke(Deployment $deployment): Deployment
    {
        $deployment->output?->writeln('Upload code package');

        $url = $deployment->client->getCodePackageUploadUrl(
            $deployment->config->getId(), $deployment->context['id'], [
                'type' => 'code',
                'checksum' => sha1_file($deployment->zipPath()),
                'filesize' => filesize($deployment->zipPath()),
            ]
        );

        (new Client)->put($url, [
            'body' => Psr7\Utils::tryFopen($deployment->zipPath(), 'r'),
        ]);

        return $deployment;
    }
}
