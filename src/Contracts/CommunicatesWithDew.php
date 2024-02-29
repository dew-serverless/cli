<?php

namespace Dew\Cli\Contracts;

interface CommunicatesWithDew
{
    /**
     * Execute the command against the environment.
     *
     * @return array<string, mixed>
     */
    public function runCommand(int $projectId, string $environment, string $command): array;

    /**
     * Retrieve the command invocation status.
     *
     * @return array<string, mixed>
     */
    public function getCommand(int $projectId, string $environment, int $commandId): array;
}
