<?php

declare(strict_types=1);

namespace Dew\Cli\Database;

use Dew\Cli\Contracts\ServerlessInstanceQuotation as QuotationContract;

class ServerlessInstanceQuotation extends InstanceQuotation implements QuotationContract
{
    /**
     * The price of the minimal RCU.
     */
    protected float $minRcuPrice;

    /**
     * The price of the maximal RCU.
     */
    protected float $maxRcuPrice;

    /**
     * Set the minimal RCU price.
     */
    public function setMinRcuPrice(float $price): self
    {
        $this->minRcuPrice = $price;

        return $this;
    }

    /**
     * The price of the minimal RDS Capacity Unit.
     */
    public function getMinRcuPrice(): float
    {
        return $this->minRcuPrice;
    }

    /**
     * Set the maximal RCU price.
     */
    public function setMaxRcuPrice(float $price): self
    {
        $this->maxRcuPrice = $price;

        return $this;
    }

    /**
     * The price of the maximal RDS Capacity Unit.
     */
    public function getMaxRcuPrice(): float
    {
        return $this->maxRcuPrice;
    }
}
