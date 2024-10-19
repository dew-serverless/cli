<?php

namespace Dew\Cli;

use Symfony\Component\Yaml\Yaml;

class ProjectConfig
{
    /**
     * @var array<string, mixed>
     */
    private array $config;

    public function __construct(
        private string $contents
    ) {
        $this->config = Yaml::parse($this->contents);
    }

    public static function load(): static
    {
        $contents = file_get_contents(getcwd().'/dew.yaml');

        return new static($contents);
    }

    /**
     * The project ID.
     */
    public function getId(): int
    {
        return $this->config['id'];
    }

    /**
     * @return array<string, mixed>
     */
    public function getEnvironment(string $name): array
    {
        return $this->config['environments'][$name];
    }

    public function getRaw(): string
    {
        return $this->contents;
    }
}
