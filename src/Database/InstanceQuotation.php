<?php

declare(strict_types=1);

namespace Dew\Cli\Database;

use Dew\Cli\Contracts\InstanceQuotation as QuotationContract;

class InstanceQuotation implements QuotationContract
{
    /**
     * The list of promotion rules.
     *
     * @var \Dew\Cli\Contracts\Promotion[]
     */
    protected array $promotion = [];

    /**
     * The list of coupons.
     *
     * @var \Dew\Cli\Contracts\Coupon[]
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
     *
     * @return \Dew\Cli\Contracts\Promotion[]
     */
    public function getPromotion(): array
    {
        return $this->promotion;
    }

    /**
     * Set available promotion rules.
     *
     * @param  \Dew\Cli\Contracts\Promotion[]  $rules
     */
    public function setPromotion(array $rules): self
    {
        $this->promotion = $rules;

        return $this;
    }

    /**
     * Get the list of coupons.
     *
     * @return \Dew\Cli\Contracts\Coupon[]
     */
    public function getCoupons(): array
    {
        return $this->coupons;
    }

    /**
     * Set available coupons.
     *
     * @param  \Dew\Cli\Contracts\Coupon[]  $coupons
     */
    public function setCoupons(array $coupons): self
    {
        $this->coupons = $coupons;

        return $this;
    }
}
