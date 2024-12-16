<?php

declare(strict_types=1);

namespace Dew\Cli\Deployments;

use Dew\Cli\Deployment;
use Dew\Cli\Outputs\FileUploadOutput;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7;
use Symfony\Component\Console\Output\OutputInterface;
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
                $deployment->context['id'], $build->values()->all()
            );

            foreach ($files as $file) {
                $output = $deployment->output instanceof OutputInterface
                    ? new FileUploadOutput($deployment->output, $file->getRelativePathname(), $file->getSize())
                    : null;

                (new Client)->put($urls[$file->getRelativePathname()], [
                    'headers' => [
                        'Content-Type' => $build[$file->getRelativePathname()]['mime_type'],
                    ],
                    'body' => Psr7\Utils::tryFopen($file->getPathname(), 'r'),
                    'progress' => fn ($dt, $db, $ut, $ub) => $output?->update($ut),
                ]);

                $output?->complete();
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
