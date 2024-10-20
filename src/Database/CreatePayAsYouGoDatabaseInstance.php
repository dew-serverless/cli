<?php

declare(strict_types=1);

namespace Dew\Cli\Database;

class CreatePayAsYouGoDatabaseInstance extends CreateDatabaseInstance
{
    /**
     * Get the type of database instance.
     */
    public function type(): string
    {
        return InstanceType::PAY_AS_YOU_GO;
    }
}
