<?php

namespace Dew\Cli\Contracts;

interface Promotion
{
    /**
     * Get the promotion rule ID.
     */
    public function id(): int;

    /**
     * Get the promotion rule name.
     */
    public function name(): string;

    /**
     * Get the promotion rule description.
     */
    public function description(): string;
}