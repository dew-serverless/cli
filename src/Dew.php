<?php

declare(strict_types=1);

namespace Dew\Cli;

use Dew\Cli\Configuration\FileRepository;
use Dew\Cli\Configuration\Repository;
use Dew\Cli\Contracts\Client;
use Dew\Cli\Http\Response;
use Dew\Cli\Models\Command;
use GuzzleHttp\Client as GuzzleClient;

/**
 * @phpstan-import-type User from \Dew\Cli\Contracts\Client
 */
final class Dew implements Client
{
    /**
     * The production endpoint.
     */
    private const DEFAULT_ENDPOINT = 'https://dew.work';

    /**
     * Create a new client instance.
     */
    public function __construct(
        private string $endpoint,
        private Repository $config
    ) {
        $this->endpoint = rtrim($endpoint, '/').'/';
    }

    /**
     * Create a new client instance from environment.
     */
    public static function make(?Repository $config = null): static
    {
        return new self(
            getenv('DEW_ENDPOINT') ?: self::DEFAULT_ENDPOINT,
            $config ?? FileRepository::createFromEnvironment()
        );
    }

    public function user(): Response
    {
        /** @var \Dew\Cli\Http\Response<User> */
        $response = new Response($this->client()->request('GET', '/api/user'));

        return $response;
    }

    public function createDeployment(int $projectId, array $data): array
    {
        return $this->post(
            sprintf('/api/projects/%s/deployments', $projectId), $data
        );
    }

    public function pingDeploymentCallback(int $deploymentId): void
    {
        $this->post(sprintf('/api/deployments/%s/callback', $deploymentId));
    }

    public function getCodePackageUploadUrl(int $deploymentId, array $data): string
    {
        $response = $this->post(
            sprintf('/api/deployments/%s/artifacts', $deploymentId),
            $data
        );

        return $response['data']['url'];
    }

    public function getAssetUploadUrls(int $deploymentId, array $files): array
    {
        $response = $this->post(
            sprintf('/api/deployments/%s/assets', $deploymentId),
            ['files' => $files]
        );

        return $response['data'];
    }

    public function getAvailableDatabaseZones(int $projectId, array $data): array
    {
        return $this->get(sprintf(
            '/api/projects/%s/databases/available-zones', $projectId
        ), $data);
    }

    public function getAvailableDatabaseClasses(int $projectId, array $data): array
    {
        return $this->get(sprintf(
            '/api/projects/%s/databases/available-specs', $projectId
        ), $data);
    }

    public function getDatabaseQuotation(int $projectId, array $data): array
    {
        return $this->get(sprintf(
            '/api/projects/%s/databases/quotation', $projectId
        ), $data);
    }

    public function createDatabase(int $projectId, array $data): array
    {
        return $this->post(sprintf(
            '/api/projects/%s/databases', $projectId
        ), $data);
    }

    public function runCommand(int $projectId, string $environment, string $command): Command
    {
        $response = $this->post(
            '/api/projects/'.$projectId.'/environments/'.$environment.'/commands',
            ['command' => $command]
        );

        return new Command($response['data']);
    }

    public function getCommand(int $projectId, string $environment, string $commandId): Command
    {
        $response = $this->get(sprintf(
            '/api/projects/%s/environments/%s/commands/%s',
            $projectId, $environment, $commandId
        ));

        return new Command($response['data']);
    }

    /**
     * Send the HTTP request to Dew server.
     *
     * @param  array<string, mixed>  $data
     */
    public function send(string $method, string $uri, array $data = []): mixed
    {
        $placement = $method === 'GET' ? 'query' : 'json';

        $response = $this->client()->request($method, $uri, [
            $placement => $data,
        ]);

        return json_decode((string) $response->getBody(), associative: true);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function get(string $uri, array $data = []): mixed
    {
        return $this->send('GET', $uri, $data);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function post(string $uri, array $data = []): mixed
    {
        return $this->send('POST', $uri, $data);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function delete(string $uri, array $data = []): mixed
    {
        return $this->send('DELETE', $uri, $data);
    }

    /**
     * Create a new Guzzle HTTP client.
     */
    private function client(): GuzzleClient
    {
        $headers = [
            'Accept' => 'application/json',
        ];

        if (is_string($this->config->get('token'))) {
            $headers['Authorization'] = 'Bearer '.$this->config->get('token');
        }

        return new GuzzleClient([
            'base_uri' => $this->endpoint,
            'headers' => $headers,
            'timeout' => 3.0,
            'http_errors' => false,
        ]);
    }
}
