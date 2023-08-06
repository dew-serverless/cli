<?php

namespace Dew\Cli\Database;

class QuotePayAsYouGoDatabaseInstance extends QuoteDatabaseInstance
{
    /**
     * Get the database instance type.
     */
    public function type(): string
    {
        return InstanceType::PAY_AS_YOU_GO;
    }
}