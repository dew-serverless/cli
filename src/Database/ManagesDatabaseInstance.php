<?php

declare(strict_types=1);

namespace Dew\Cli\Database;

trait ManagesDatabaseInstance
{
    /**
     * The database engine.
     */
    public string $engine;

    /**
     * The database engine version.
     */
    public string $engineVersion;

    /**
     * The database deployment option.
     */
    public string $deployment;

    /**
     * The database instance class.
     */
    public string $class;

    /**
     * The storage size in GB.
     */
    public int $storage;

    /**
     * The storage type.
     */
    public string $storageType;

    /**
     * Configure database engine.
     */
    public function engine(string $engine): self
    {
        $this->engine = $engine;

        return $this;
    }

    /**
     * Configure database engine version.
     */
    public function engineVersion(string $version): self
    {
        $this->engineVersion = $version;

        return $this;
    }

    /**
     * Configure database deployment option.
     */
    public function deployment(string $option): self
    {
        $this->deployment = $option;

        return $this;
    }

    /**
     * Configure database instance class.
     */
    public function class(string $class): self
    {
        $this->class = $class;

        return $this;
    }

    /**
     * Configure database instance storage size in GB.
     */
    public function storage(int $storage): self
    {
        $this->storage = $storage;

        return $this;
    }

    /**
     * Configure database instance storage type.
     */
    public function storageType(string $type): self
    {
        $this->storageType = $type;

        return $this;
    }
}
