<?php

declare(strict_types=1);

namespace Dew\Cli;

final class FileManifest implements Manifest
{
    /**
     * The manifest file path.
     */
    private string $filePath;

    /**
     * The manifest data.
     *
     * @var array<string, mixed>
     */
    private ?array $data = null;

    /**
     * Create a manifest instance.
     */
    public function __construct(
        private string $rootPath
    ) {
        $this->filePath = $this->rootPath.'/.dew/project.json';
        $this->refresh();
    }

    /**
     * Create a manifest instance.
     */
    public static function make(string $rootPath): self
    {
        return new self($rootPath);
    }

    /**
     * Determine if the manifest exists.
     */
    public function exists(): bool
    {
        return $this->data !== null;
    }

    /**
     * Reload the latest data from the manifest.
     */
    public function refresh(): void
    {
        if (! file_exists($this->filePath)) {
            $this->data = null;

            return;
        }

        $this->data = json_decode(
            file_get_contents($this->filePath),
            associative: true,
            flags: JSON_THROW_ON_ERROR
        );
    }

    /**
     * Persist data to the manifest.
     *
     * @param  array<string, mixed>  $data
     */
    public function write(array $data): void
    {
        $this->ensureDirectoryExists();

        $contents = json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR);
        file_put_contents($this->filePath, $contents, LOCK_EX);

        chmod($this->filePath, 0644);

        $this->data = $data;
    }

    /**
     * Retrieve an item from the manifest.
     */
    public function get(string $key, mixed $default = null): mixed
    {
        if ($this->data === null) {
            return $default;
        }

        return $this->data[$key] ?? $default;
    }

    /**
     * Ensure the manifest directory exists.
     */
    private function ensureDirectoryExists(): void
    {
        $directory = dirname($this->filePath);

        if (! is_dir($directory)) {
            mkdir($directory, 0755, recursive: true);
        }
    }

    /**
     * Retrieve all items from the manifest.
     *
     * @return array<string, mixed>
     */
    public function all(): array
    {
        if ($this->data === null) {
            throw new \RuntimeException(sprintf(
                'Manifest file %s does not exist.', $this->filePath
            ));
        }

        return $this->data;
    }
}
