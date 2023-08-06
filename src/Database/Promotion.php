<?php

namespace Dew\Cli\Database;

use Dew\Cli\Contracts\Promotion as PromotionContract;

class Promotion implements PromotionContract
{
    public function __construct(
        protected int $id,
        protected string $name,
        protected string $description
    ) {
        //
    }

    /**
     * Get the promotion rule ID.
     */
    public function id(): int
    {
        return $this->id;
    }

    /**
     * Get the promotion rule name.
     */
    public function name(): string
    {
        return $this->name;
    }

    /**
     * Get the promotion rule description.
     */
    public function description(): string
    {
        return $this->description;
    }
}