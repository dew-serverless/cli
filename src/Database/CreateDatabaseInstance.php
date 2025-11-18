<?php

declare(strict_types=1);

namespace Dew\Cli\Database;

use Dew\Cli\Contracts\Client;

abstract class CreateDatabaseInstance
{
    use ManagesDatabaseInstance, ManagesDatabaseInstanceNetwork;

    /**
     * The database instance name.
     */
    public string $name;

    public function __construct(
        private Client $client,
        private int    $projectId
    ) {
        //
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
     *
     * @return array<string, mixed>
     */
    public function create(): array
    {
        return $this->client->createDatabase(
            $this->projectId, $this->toAcsRequest()
        )['data'];
    }

    /**
     * Represent as database creation request.
     *
     * @return array<string, mixed>
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
