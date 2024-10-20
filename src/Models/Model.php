<?php

declare(strict_types=1);

namespace Dew\Cli\Models;

use RuntimeException;

abstract class Model
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function __construct(
        private array $attributes
    ) {
        //
    }

    public function offsetExists(int|string $offset): bool
    {
        return isset($this->attributes[$offset]);
    }

    public function offsetGet(int|string $offset): mixed
    {
        return $this->attributes[$offset];
    }

    public function offsetSet(int|string $offset, mixed $value): void
    {
        throw new RuntimeException(sprintf(
            'The %s model is immutable.', static::class
        ));
    }

    public function offsetUnset(int|string $offset): void
    {
        throw new RuntimeException(sprintf(
            'The %s model is immutable.', static::class
        ));
    }

    public function __get(string $name): mixed
    {
        return $this->attributes[$name] ?? null;
    }
}
