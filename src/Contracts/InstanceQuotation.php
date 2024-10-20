<?php

declare(strict_types=1);

namespace Dew\Cli\Contracts;

interface InstanceQuotation
{
    /**
     * Get the currency.
     */
    public function getCurrency(): string;

    /**
     * Get the original price.
     */
    public function getOriginalPrice(): float;

    /**
     * Get the discount.
     */
    public function getDiscount(): float;

    /**
     * Get the trade price.
     */
    public function getTradePrice(): float;

    /**
     * Get the list of promotion rules.
     *
     * @return \Dew\Cli\Contracts\Promotion[]
     */
    public function getPromotion(): array;

    /**
     * Get the list of coupons;
     *
     * @return \Dew\Cli\Contracts\Coupon[]
     */
    public function getCoupons(): array;
}
