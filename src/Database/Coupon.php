<?php

namespace Dew\Cli\Database;

use Dew\Cli\Contracts\Coupon as CouponContract;

class Coupon implements CouponContract
{
    public function __construct(
        protected string $number,
        protected string $name,
        protected string $description,
        protected bool $isSelected
    ) {
        //
    }

    /**
     * Whether the coupon is selected.
     */
    public function isSelected(): bool
    {
        return $this->isSelected;
    }

    /**
     * Get the coupon number.
     */
    public function number(): string
    {
        return $this->number;
    }

    /**
     * Get the coupon name.
     */
    public function name(): string
    {
        return $this->name;
    }

    /**
     * Get the coupon description.
     */
    public function description(): string
    {
        return $this->description;
    }
}
