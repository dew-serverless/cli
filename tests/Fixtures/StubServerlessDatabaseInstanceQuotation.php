<?php

declare(strict_types=1);

namespace Dew\Cli\Tests\Fixtures;

use Dew\Cli\Contracts\ServerlessInstanceQuotation;

class StubServerlessDatabaseInstanceQuotation extends StubDatabaseInstanceQuotation implements ServerlessInstanceQuotation
{
    public function getMinRcuPrice(): float
    {
        return 0.19;
    }

    public function getMaxRcuPrice(): float
    {
        return 0.39;
    }
}
