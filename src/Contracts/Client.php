<?php

declare(strict_types=1);

namespace Dew\Cli\Contracts;

use Dew\Cli\Http\Response;
use Dew\Cli\Models\Command;

/**
 * @phpstan-type User array{id: int, name: string, email: string}
 * @phpstan-type Team array{id: string, user_id: int, name: string, personal_team: bool, created_at: string, updated_at: string}
 * @phpstan-type Project array{id: int, name: string, slug: string, region: string, status: string, is_acs_connected: bool, team?: Team, created_at: string, updated_at: string}
 */
interface Client
{
    /**
     * Configure the access token.
     */
    public function setToken(string $token): void;

    /**
     * The authenticated user.
     *
     * @return \Dew\Cli\Http\Response<User>
     */
    public function user(): Response;

    /**
     * The available regions.
     *
     * @return \Dew\Cli\Http\Response<array{data: string[]}>
     */
    public function regions(): Response;

    /**
     * Create a new project.
     *
     * @param  array{name: string, slug: string, region: string}  $data
     * @return \Dew\Cli\Http\Response<array{data: Project}>
     */
    public function createProject(array $data): Response;

    /**
     * Check the project slug availability.
     *
     * @return \Dew\Cli\Http\Response<array{data: array{available: bool}}>
     */
    public function checkProjectSlug(string $slug): Response;

    /**
     * Retrieve a project by ID.
     *
     * @return \Dew\Cli\Http\Response<array{data: Project}>
     */
    public function getProject(int $projectId): Response;

    /**
     * List projects of the current team.
     *
     * @return \Dew\Cli\Http\Response<array{data: Project[]}>
     */
    public function listProjects(): Response;

    /**
     * Connect the project with Alibaba Cloud.
     *
     * @param  array{access_key_id: string, access_key_secret: string}  $data
     * @return \Dew\Cli\Http\Response<array<string, mixed>>
     */
    public function connectAcsAccount(int $projectId, array $data): Response;

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
    public function getCodePackageUploadUrl(int $deploymentId, array $data): string;

    /**
     * Get URLs to upload assets for the deployment.
     *
     * @param  array<int, array{path: string, filesize: int, mime_type: string, checksum: string}>  $files
     * @return array<string, string>
     */
    public function getAssetUploadUrls(int $deploymentId, array $files): array;

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
