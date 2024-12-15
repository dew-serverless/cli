<?php

declare(strict_types=1);

namespace Dew\Cli\Filesize;

final class Byte implements Size
{
    use HandlesSize;

    public function __construct(
        private int $size
    ) {
        //
    }

    public static function fromByte(int $bytes): static
    {
        return new static($bytes);
    }

    public function symbol(): string
    {
        return 'B';
    }
}
