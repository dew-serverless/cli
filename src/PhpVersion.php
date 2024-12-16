<?php

declare(strict_types=1);

namespace Dew\Cli;

use Composer\Semver\Semver;

final class PhpVersion
{
    /**
     * The supported PHP versions.
     *
     * @var string[]
     */
    const SUPPORTED = [
        '8.4',
        '8.3',
        '8.2',
        '8.1',
        '8.0',
    ];

    /**
     * Find a satisfied PHP version from the composer.json file.
     */
    public static function fromComposer(string $path): ?string
    {
        if (! file_exists($path)) {
            return null;
        }

        $composer = json_decode(file_get_contents($path), associative: true);

        if (! isset($composer['require']['php'])) {
            return null;
        }

        $versions = Semver::satisfiedBy(self::SUPPORTED, $composer['require']['php']);

        return $versions[0] ?? null;
    }

    public static function fromRuntime(): ?string
    {
        $version = sprintf('%s.%s', PHP_MAJOR_VERSION, PHP_MINOR_VERSION);

        if (! in_array($version, self::SUPPORTED)) {
            return null;
        }

        return $version;
    }
}
