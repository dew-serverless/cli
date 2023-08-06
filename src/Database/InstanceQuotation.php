<?php

namespace Dew\Cli\Database;

use Dew\Cli\Contracts\InstanceQuotation as QuotationContract;

class InstanceQuotation implements QuotationContract
{
    /**
     * The list of promotion rules.
     */
    protected array $promotion = [];

    /**
     * The list of coupons.
     */
    protected array $coupons = [];

    public function __construct(
        protected string $currency,
        protected float $originalPrice,
        protected float $discount,
        protected float $tradePrice
    ) {
        //
    }

    /**
     * Get the currency.
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }

    /**
     * Get the original price.
     */
    public function getOriginalPrice(): float
    {
        return $this->originalPrice;
    }

    /**
     * Get the discount.
     */
    public function getDiscount(): float
    {
        return $this->discount;
    }

    /**
     * Get the trade price.
     */
    public function getTradePrice(): float
    {
        return $this->tradePrice;
    }

    /**
     * Get the list of promotion rules.
     */
    public function getPromotion(): array
    {
        return $this->promotion;
    }

    /**
     * Set available promotion rules.
     */
    public function setPromotion(array $rules): self
    {
        $this->promotion = $rules;

        return $this;
    }

    /**
     * Get the list of coupons.
     */
    public function getCoupons(): array
    {
        return $this->coupons;
    }

    /**
     * Set available coupons.
     */
    public function setCoupons(array $coupons): self
    {
        $this->coupons = $coupons;

        return $this;
    }
}