<?php

declare(strict_types=1);

namespace Dew\Cli;

use Dew\Cli\Contracts\CommunicatesWithDew;
use Dew\Cli\Models\Command;
use GuzzleHttp\Client as GuzzleClient;

final class Client implements CommunicatesWithDew
{
    /**
     * Create a new Dew client.
     */
    public function __construct(
        private string $endpoint,
        private string $token
    ) {
        //
    }

    /**
     * Create a new Dew client from the configuration setup.
     *
     * @param  array<string, mixed>  $config
     */
    public static function make(array $config = []): static
    {
        return new self(
            $config['endpoint'] ?? getenv('DEW_ENDPOINT'),
            $config['token'] ?? Configuration::createFromEnvironment()->getToken()
        );
    }

    public function createDeployment(int $projectId, string $environment, array $data): array
    {
        return $this->post(sprintf(
            '/api/projects/%s/environments/%s/deployments',
            $projectId, $environment
        ), $data);
    }

    public function pingDeploymentCallback(string $callbackUrl): void
    {
        $this->post($callbackUrl);
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
     * Make a new Guzzle client.
     */
    private function client(): GuzzleClient
    {
        return new GuzzleClient([
            'base_uri' => $this->endpoint,
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer '.$this->token,
            ],
        ]);
    }
}
