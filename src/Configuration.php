<?php

declare(strict_types=1);

namespace Dew\Cli;

final class Configuration
{
    public function __construct(
        private string $path
    ) {
        //
    }

    public static function createFromEnvironment(): static
    {
        $user = posix_getpwuid(posix_getuid());

        return new self($user['dir'].'/.dew');
    }

    public function setToken(string $accessToken): self
    {
        return $this->set('token', $accessToken);
    }

    public function getToken(): string
    {
        return $this->get('token');
    }

    public function set(string $item, string $value): self
    {
        $this->ensureExists();

        $config = $this->get();

        $config[$item] = $value;

        file_put_contents($this->path.'/config.json', json_encode($config), LOCK_EX);

        return $this;
    }

    public function get(?string $item = null): mixed
    {
        $config = $this->exists()
            ? json_decode(file_get_contents($this->path.'/config.json'), associative: true)
            : [];

        return is_null($item) ? $config : $config[$item] ?? null;
    }

    public function exists(): bool
    {
        return is_dir($this->path) && file_exists($this->path.'/config.json');
    }

    public function path(): string
    {
        return $this->path;
    }

    private function ensureExists(): void
    {
        if ($this->exists()) {
            return;
        }

        $this->createPath();
        $this->createEmptyStore();
    }

    private function createPath(): void
    {
        mkdir($this->path, 0700, recursive: true);
    }

    private function createEmptyStore(): void
    {
        file_put_contents($this->path.'/config.json', json_encode([]), LOCK_EX);

        chmod($this->path.'/config.json', 0600);
    }
}
