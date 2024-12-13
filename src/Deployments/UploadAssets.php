<?php

declare(strict_types=1);

namespace Dew\Cli\Deployments;

use Dew\Cli\Deployment;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class UploadAssets
{
    /**
     * Execute the job.
     */
    public function __invoke(Deployment $deployment): Deployment
    {
        $deployment->output?->writeln('Upload assets');

        $assets = $this->files()->in(
            $publicPath = Path::join($deployment->buildDir(), $deployment->publicPath())
        );

        $i = 0;
        $cursor = -1;
        $chunks = [];
        foreach ($assets as $file) {
            if ($i % 25 === 0) {
                $cursor++;
            }

            $chunks[$cursor][] = $file;

            $i++;
        }

        foreach ($chunks as $files) {
            $build = collect($files)->flatMap(fn (SplFileInfo $file): array => [
                $file->getRelativePathname() => [
                    'path' => $file->getRelativePathname(),
                    'filesize' => $file->getSize(),
                    'mime_type' => Psr7\MimeType::fromFilename($file->getFilename()),
                    'checksum' => sha1_file($file->getPathname()),
                ],
            ]);

            $urls = $deployment->client->getAssetUploadUrls(
                $deployment->config->getId(), $deployment->context['id'],
                $build->values()->all()
            );

            foreach ($files as $file) {
                $deployment->output?->writeln($file->getRelativePathname());

                (new Client)->put($urls[$file->getRelativePathname()], [
                    'headers' => [
                        'Content-Type' => $build[$file->getRelativePathname()]['mime_type'],
                    ],
                    'body' => Psr7\Utils::tryFopen($file->getPathname(), 'r'),
                ]);
            }
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
