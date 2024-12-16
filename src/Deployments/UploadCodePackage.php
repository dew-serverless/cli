<?php

declare(strict_types=1);

namespace Dew\Cli\Deployments;

use Dew\Cli\Deployment;
use Dew\Cli\Outputs\FileUploadOutput;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7;
use Symfony\Component\Console\Output\OutputInterface;

class UploadCodePackage
{
    public function __invoke(Deployment $deployment): Deployment
    {
        $deployment->output?->writeln('Upload code package');

        $url = $deployment->client->getCodePackageUploadUrl(
            $deployment->context['id'], [
                'type' => 'code',
                'checksum' => sha1_file($deployment->zipPath()),
                'filesize' => $filesize = filesize($deployment->zipPath()),
            ]
        );

        $output = $deployment->output instanceof OutputInterface
            ? new FileUploadOutput($deployment->output, $deployment->zipName(), $filesize)
            : null;

        (new Client)->put($url, [
            'body' => Psr7\Utils::tryFopen($deployment->zipPath(), 'r'),
            'progress' => fn ($dt, $db, $ut, $ub) => $output?->update($ub),
        ]);

        $output?->complete();

        return $deployment;
    }
}
