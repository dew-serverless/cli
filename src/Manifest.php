<?php

declare(strict_types=1);

namespace Dew\Cli;

interface Manifest
{
    /**
     * Determine if the manifest exists.
     */
    public function exists(): bool;

    /**
     * Retrieve an item from the manifest.
     */
    public function get(string $key, mixed $default = null): mixed;

    /**
     * Persist data to the manifest.
     *
     * @param  array<string, mixed>  $data
     */
    public function write(array $data): void;

    /**
     * Reload the latest data from the manifest.
     */
    public function refresh(): void;

    /**
     * Retrieve all items from the manifest.
     *
     * @return array<string, mixed>
     */
    public function all(): array;
}
