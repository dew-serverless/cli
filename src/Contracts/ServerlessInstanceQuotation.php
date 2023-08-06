<?php

namespace Dew\Cli\Contracts;

interface ServerlessInstanceQuotation extends InstanceQuotation
{
    /**
     * The price of the minimal RDS Capacity Unit.
     */
    public function getMinRcuPrice(): float;

    /**
     * The price of the maximal RDS Capacity Unit.
     */
    public function getMaxRcuPrice(): float;
}