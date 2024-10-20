<?php

declare(strict_types=1);

namespace Dew\Cli\Database;

use Dew\Cli\Contracts\DatabaseStorageRange;

class StorageRange implements DatabaseStorageRange
{
    public function __construct(
        protected int $min,
        protected int $max,
        protected int $step
    ) {
        //
    }

    /**
     * The minimal valid storage size in GB.
     */
    public function min(): int
    {
        return $this->min;
    }

    /**
     * The maximal valid storage size in GB.
     */
    public function max(): int
    {
        return $this->max;
    }

    /**
     * The valid step for increasing storage size.
     */
    public function step(): int
    {
        return $this->step;
    }
}
