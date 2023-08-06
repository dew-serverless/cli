<?php

namespace Dew\Cli\Database;

use Dew\Cli\Client;
use Dew\Cli\InteractsWithDew;

abstract class CreateDatabaseInstance
{
    use InteractsWithDew, ManagesDatabaseInstance, ManagesDatabaseInstanceNetwork;

    /**
     * The project ID.
     */
    public int $projectId;

    /**
     * The database instance name.
     */
    public string $name;

    /**
     * Configure project ID.
     */
    public function forProject(int $projectId): self
    {
        $this->projectId = $projectId;

        return $this;
    }

    /**
     * Configure database instance name.
     */
    public function name(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get the type of database instance.
     */
    abstract public function type(): string;

    /**
     * Create database instance.
     */
    public function create(): array
    {
        $response = Client::make()
            ->post('/api/projects/'.$this->projectId.'/databases', [
                'headers' => [
                    'Accept' => 'application/json',
                    'Authorization' => sprintf('Bearer %s', $this->token),
                ],
                'json' => $this->toAcsRequest(),
            ]);

        $decoded = json_decode($response->getBody()->getContents(), associative: true);

        return $decoded['data'];
    }

    /**
     * Represent as database creation request.
     */
    protected function toAcsRequest(): array
    {
        return [
            'type' => $this->type(),
            'name' => $this->name,
            'engine' => $this->engine,
            'engine_version' => $this->engineVersion,
            'deployment' => $this->deployment,
            'class' => $this->class,
            'storage' => $this->storage,
            'storage_type' => $this->storageType,
        ];
    }
}