<?php

declare(strict_types=1);

namespace Dew\Cli\Configuration;

final class FileRepository implements Repository
{
    /**
     * The configuration storage.
     *
     * @var array<string, mixed>
     */
    private array $config = [];

    /**
     * Create a repository instance.
     */
    public function __construct(
        private string $path
    ) {
        $this->load();
    }

    /**
     * Create a repository instance from the environment context.
     */
    public static function createFromEnvironment(): self
    {
        $dir = getenv('XDG_CONFIG_HOME');

        if ($dir === false || $dir === '') {
            $home = getenv('HOME');

            if ($home === false || $home === '') {
                throw new \RuntimeException('Cannot determine home directory for configuration storage.');
            }

            $dir = $home.'/.config';
        }

        if (! file_exists($dir.'/dew')
            && ! mkdir($dir.'/dew', 0700, recursive: true)) {
            throw new \RuntimeException("Failed to create configuration directory: {$dir}/dew");
        }

        if (! file_exists($dir.'/dew/config.json')) {
            file_put_contents($dir.'/dew/config.json', '{}', LOCK_EX);
            chmod($dir.'/dew/config.json', 0600);
        }

        return new self($dir.'/dew/config.json');
    }

    /**
     * Load configurations from the path.
     */
    private function load(): void
    {
        $contents = file_get_contents($this->path);

        if ($contents === false) {
            throw new \RuntimeException("Failed to read configuration file: {$this->path}");
        }

        $decoded = json_decode($contents, associative: true, flags: JSON_THROW_ON_ERROR);
        $this->config = is_array($decoded) ? $decoded : [];
    }

    /**
     * Store a configuration item.
     */
    public function set(string $key, mixed $value): self
    {
        $this->config[$key] = $value;

        return $this->persist();
    }

    /**
     * Retrieve a configuration value.
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return $this->config[$key] ?? $default;
    }

    /**
     * Determine if configurations have a given item.
     */
    public function has(string $key): bool
    {
        return array_key_exists($key, $this->config);
    }

    /**
     * Remove a configuration item.
     */
    public function remove(string $key): self
    {
        unset($this->config[$key]);

        return $this->persist();
    }

    /**
     * Retrieve all configurations.
     *
     * @return array<string, mixed>
     */
    public function all(): array
    {
        return $this->config;
    }

    /**
     * Empty all configurations.
     */
    public function flush(): self
    {
        $this->config = [];

        return $this->persist();
    }

    /**
     * Persist configurations to the path.
     */
    private function persist(): self
    {
        $contents = json_encode(
            $this->config,
            JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR
        );

        if (file_put_contents($this->path, $contents, LOCK_EX) === false) {
            throw new \RuntimeException("Failed to write configuration file: {$this->path}");
        }

        chmod($this->path, 0600);

        return $this;
    }
}
