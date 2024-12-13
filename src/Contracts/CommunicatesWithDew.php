<?php

declare(strict_types=1);

namespace Dew\Cli\Contracts;

use Dew\Cli\Models\Command;

interface CommunicatesWithDew
{
    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function createDeployment(int $projectId, array $data): array;

    public function pingDeploymentCallback(int $deploymentId): void;

    /**
     * Get the URL to upload the code package for the deployment.
     *
     * @param  array<string, mixed>  $data
     */
    public function getCodePackageUploadUrl(int $project, int $deploymentId, array $data): string;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function getAvailableDatabaseZones(int $projectId, array $data): array;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function getAvailableDatabaseClasses(int $projectId, array $data): array;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function getDatabaseQuotation(int $projectId, array $data): array;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function createDatabase(int $projectId, array $data): array;

    /**
     * Execute the command against the environment.
     */
    public function runCommand(int $projectId, string $environment, string $command): Command;

    /**
     * Retrieve the command invocation status.
     */
    public function getCommand(int $projectId, string $environment, string $commandId): Command;
}
