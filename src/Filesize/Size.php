<?php

declare(strict_types=1);

namespace Dew\Cli\Filesize;

interface Size
{
    public static function fromByte(int $bytes): Size;

    public function size(): int|float;

    public function round(?int $precision = null): int|float;

    public function symbol(): string;
}
