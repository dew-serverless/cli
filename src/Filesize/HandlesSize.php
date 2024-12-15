<?php

declare(strict_types=1);

namespace Dew\Cli\Filesize;

trait HandlesSize
{
    public function size(): int|float
    {
        return $this->size;
    }

    public function round(?int $precision = null): int|float
    {
        return Filesize::round($this->size, $precision);
    }
}
