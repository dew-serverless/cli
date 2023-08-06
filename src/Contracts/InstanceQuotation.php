<?php

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
     */
    public function getPromotion(): array;

    /**
     * Get the list of coupons;
     */
    public function getCoupons(): array;
}