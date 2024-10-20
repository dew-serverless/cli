<?php

declare(strict_types=1);

namespace Dew\Cli\Tests\Fixtures;

use Dew\Cli\Contracts\InstanceQuotation;

class StubDatabaseInstanceQuotation implements InstanceQuotation
{
    public function getCurrency(): string
    {
        return 'CNY';
    }

    public function getOriginalPrice(): float
    {
        return 0.49;
    }

    public function getDiscount(): float
    {
        return 0;
    }

    public function getTradePrice(): float
    {
        return 0.49;
    }

    public function getPromotion(): array
    {
        return [];
    }

    public function getCoupons(): array
    {
        return [];
    }
}
