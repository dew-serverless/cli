<?php

declare(strict_types=1);

namespace Dew\Cli\Filesize;

final class Filesize
{
    /**
     * Create a size instance with an appropriate representation.
     */
    public static function make(int $bytes): Size
    {
        return match (true) {
            $bytes >= 1024 ** 2 => Mebibyte::fromByte($bytes),
            $bytes >= 1024 => Kibibyte::fromByte($bytes),
            default => Byte::fromByte($bytes),
        };
    }

    /**
     * Round the filesize.
     */
    public static function round(int|float $size, int $precision = 0): float
    {
        return round($size, $precision);
    }
}
