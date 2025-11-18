<?php

declare(strict_types=1);

namespace Dew\Cli\Configuration;

interface Repository
{
    /**
     * Store a configuration item.
     */
    public function set(string $key, mixed $value): self;

    /**
     * Retrieve a configuration value.
     */
    public function get(string $key, mixed $default = null): mixed;

    /**
     * Determine if configurations have a given item.
     */
    public function has(string $key): bool;

    /**
     * Remove a configuration item.
     */
    public function remove(string $key): self;

    /**
     * Retrieve all configurations.
     *
     * @return array<string, mixed>
     */
    public function all(): array;

    /**
     * Empty all configurations.
     */
    public function flush(): self;
}
