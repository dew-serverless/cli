<?php

namespace Dew\Cli\Database;

use Dew\Cli\Contracts\CommunicatesWithDew;

abstract class CreateDatabaseInstance
{
    use ManagesDatabaseInstance, ManagesDatabaseInstanceNetwork;

    /**
     * The database instance name.
     */
    public string $name;

    public function __construct(
        private CommunicatesWithDew $client,
        private int $projectId
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
     */
    public function create(): array
    {
        return $this->client->createDatabase(
            $this->projectId, $this->toAcsRequest()
        )['data'];
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
