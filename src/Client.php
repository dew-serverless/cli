<?php

namespace Dew\Cli;

use Dew\Cli\Contracts\CommunicatesWithDew;
use GuzzleHttp\Client as BaseClient;

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
        $response = $this->client()
            ->post('/api/projects/'.$projectId.'/environments/'.$environment.'/commands', [
                'json' => [
                    'command' => $command,
                ],
            ]);

        return json_decode((string) $response->getBody(), associative: true);
    }

    /**
     * Retrieve the command invocation status.
     *
     * @return array<string, mixed>
     */
    public function getCommand(int $projectId, string $environment, int $commandId): array
    {
        $response = $this->client()
            ->get('/api/projects/'.$projectId.'/environments/'.$environment.'/commands/'.$commandId);

        return json_decode((string) $response->getBody(), associative: true);
    }

    /**
     * Make a new Guzzle client.
     */
    protected function client(): BaseClient
    {
        return new BaseClient([
            'base_uri' => $this->endpoint,
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer '.$this->token,
            ],
        ]);
    }
}
