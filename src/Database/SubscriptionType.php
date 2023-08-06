<?php

namespace Dew\Cli\Database;

class SubscriptionType
{
    const YEAR = 'year';
    const MONTH = 'month';

    /**
     * Retrieve all the possible values.
     */
    public static function all(): array
    {
        return [
            self::YEAR,
            self::MONTH,
        ];
    }
}