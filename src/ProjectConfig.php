<?php

namespace Dew\Cli;

use Symfony\Component\Yaml\Yaml;

class ProjectConfig
{
    public function __construct(
        protected array $config
    ) {
        //
    }

    public static function load(): static
    {
        $config = Yaml::parse(file_get_contents(getcwd().'/dew.yaml'));

        return new static($config);
    }

    /**
     * Retrieve the value of configuration item.
     */
    public function get(string $item, mixed $default = null): mixed
    {
        return $this->config[$item] ?? $default;
    }
}