<?php

declare(strict_types=1);

namespace Dew\Cli\Models;

/**
 * @property string $id
 * @property string $input
 * @property int $status
 * @property string|null $command_line
 * @property int|null $exit_code
 * @property string|null $output
 * @property int|null $duration_ms
 * @property string $acs_request_id
 */
final class Command extends Model
{
    const STATUS_RUNNING = 0;

    public function isRunning(): bool
    {
        return $this->status === self::STATUS_RUNNING;
    }
}
