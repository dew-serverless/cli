<?php

declare(strict_types=1);

namespace Dew\Cli\Configuration;

interface Repository
{
    /**
     * Store a config value.
     */
    public function set(string $key, mixed $value): self;

    /**
     * Retrieve a config value.
     */
    public function get(string $key, mixed $default = null): mixed;

    /**
     * Determine if the config has a given key.
     */
    public function has(string $key): bool;

    /**
     * Remove a config value.
     */
    public function remove(string $key): self;

    /**
     * Retrieve all config values.
     *
     * @return array<string, mixed>
     */
    public function all(): array;

    /**
     * Remove all config values.
     */
    public function flush(): self;
}
