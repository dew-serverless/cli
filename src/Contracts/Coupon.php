<?php

declare(strict_types=1);

namespace Dew\Cli\Contracts;

interface Coupon
{
    /**
     * Whether the coupon is selected.
     */
    public function isSelected(): bool;

    /**
     * Get the coupon number.
     */
    public function number(): string;

    /**
     * Get the coupon name.
     */
    public function name(): string;

    /**
     * Get the coupon description.
     */
    public function description(): string;
}
