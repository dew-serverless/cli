<?php

namespace Dew\Cli\Database;

class SubscriptionType
{
    const YEAR = 'year';
    const MONTH = 'month';

    /**
     * Retrieve all the possible values.
     *
     * @return string[]
     */
    public static function all(): array
    {
        return [
            self::YEAR,
            self::MONTH,
        ];
    }
}
