<?php

declare(strict_types=1);

namespace Dew\Cli\Contracts;

interface DatabaseStorageRange
{
    /**
     * The minimal valid storage size in GB.
     */
    public function min(): int;

    /**
     * The maximal valid storage size in GB.
     */
    public function max(): int;

    /**
     * The valid step for increasing storage size.
     */
    public function step(): int;
}
