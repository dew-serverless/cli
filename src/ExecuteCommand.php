<?php

namespace Dew\Cli;

class ExecuteCommand
{
    use InteractsWithDew;

    /**
     * The project ID.
     */
    public int $projectId;

    /**
     * The execution environment.
     */
    public string $environment;

    /**
     * Configure project ID.
     */
    public function forProject(int $projectId): self
    {
        $this->projectId = $projectId;

        return $this;
    }

    /**
     * Configure execution environment.
     */
    public function on(string $environment): self
    {
        $this->environment = $environment;

        return $this;
    }

    /**
     * Execute the given command.
     */
    public function execute(string $command): array
    {
        $response = Client::make()
            ->post('/api/projects/'.$this->projectId.'/environments/'.$this->environment.'/cli', [
                'headers' => [
                    'Accept' => 'application/json',
                    'Authorization' => sprintf('Bearer %s', $this->token),
                ],
                'json' => [
                    'command' => $command,
                ],
            ]);

        return json_decode($response->getBody()->getContents(), associative: true);
    }
}
