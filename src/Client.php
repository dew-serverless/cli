<?php

namespace Dew\Cli;

use Dew\Cli\Contracts\CommunicatesWithDew;
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
     */
    public static function make(): static
    {
        return new self(
            getenv('DEW_ENDPOINT'),
            Configuration::createFromEnvironment()->getToken()
        );
    }

    /**
     * Execute the command against the environment.
     *
     * @return array<string, mixed>
     */
    public function runCommand(int $projectId, string $environment, string $command): array
    {
        return $this->send('POST', '/api/projects/'.$projectId.'/environments/'.$environment.'/commands', [
            'command' => $command,
        ]);
    }

    /**
     * Retrieve the command invocation status.
     *
     * @return array<string, mixed>
     */
    public function getCommand(int $projectId, string $environment, int $commandId): array
    {
        return $this->send('GET', '/api/projects/'.$projectId.'/environments/'.$environment.'/commands/'.$commandId);
    }

    /**
     * Send the HTTP request to Dew server.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function send(string $method, string $uri, array $data = []): array
    {
        $response = $this->client()->request($method, $uri, [
            'json' => $data
        ]);

        return json_decode((string) $response->getBody(), associative: true);
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
