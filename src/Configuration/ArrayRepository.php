<?php

declare(strict_types=1);

namespace Dew\Cli\Configuration;

final class ArrayRepository implements Repository
{
    /**
     * Create a new repository instance.
     *
     * @param  array<string, mixed>  $config
     */
    public function __construct(
        private array $config = []
    ) {
        //
    }

    /**
     * Store a config value.
     */
    public function set(string $key, mixed $value): self
    {
        $this->config[$key] = $value;

        return $this;
    }

    /**
     * Retrieve a config value.
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return $this->config[$key] ?? $default;
    }

    /**
     * Determine if the config has a given key.
     */
    public function has(string $key): bool
    {
        return array_key_exists($key, $this->config);
    }

    /**
     * Remove a config value.
     */
    public function remove(string $key): self
    {
        unset($this->config[$key]);

        return $this;
    }

    /**
     * Retrieve all config values.
     *
     * @return array<string, mixed>
     */
    public function all(): array
    {
        return $this->config;
    }

    /**
     * Remove all config values.
     */
    public function flush(): self
    {
        $this->config = [];

        return $this;
    }
}
