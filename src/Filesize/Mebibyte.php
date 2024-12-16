<?php

declare(strict_types=1);

namespace Dew\Cli\Filesize;

final class Mebibyte implements Size
{
    use HandlesSize;

    public function __construct(
        private int|float $size
    ) {
        //
    }

    public static function fromByte(int $bytes): static
    {
        return new self($bytes / 1024 ** 2);
    }

    public function symbol(): string
    {
        return 'MiB';
    }
}
