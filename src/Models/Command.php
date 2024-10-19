<?php

declare(strict_types=1);

namespace Dew\Cli\Models;

final class Command extends Model
{
    const STATUS_RUNNING = 0;

    public function isRunning(): bool
    {
        return $this->status === self::STATUS_RUNNING;
    }
}
