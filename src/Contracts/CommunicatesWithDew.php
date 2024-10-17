<?php

namespace Dew\Cli\Contracts;

use Dew\Cli\Models\Command;

interface CommunicatesWithDew
{
    /**
     * Execute the command against the environment.
     */
    public function runCommand(int $projectId, string $environment, string $command): Command;

    /**
     * Retrieve the command invocation status.
     */
    public function getCommand(int $projectId, string $environment, string $commandId): Command;
}
