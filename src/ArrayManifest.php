<?php

declare(strict_types=1);

namespace Dew\Cli;

final class ArrayManifest implements Manifest
{
    /**
     * Create a manifest instance.
     *
     * @param  array<string, mixed>  $data
     */
    public function __construct(
        private array $data = []
    ) {
        //
    }

    /**
     * Determine if the manifest exists.
     */
    public function exists(): bool
    {
        return $this->data !== [];
    }

    /**
     * Retrieve an item from the manifest.
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return $this->data[$key] ?? $default;
    }

    /**
     * Persist data to the manifest.
     *
     * @param  array<string, mixed>  $data
     */
    public function write(array $data): void
    {
        $this->data = $data;
    }

    /**
     * Reload the latest data from the manifest.
     */
    public function refresh(): void
    {
        //
    }

    /**
     * Retrieve all items from the manifest.
     *
     * @return array<string, mixed>
     */
    public function all(): array
    {
        return $this->data;
    }
}
