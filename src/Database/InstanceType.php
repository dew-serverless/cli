<?php

declare(strict_types=1);

namespace Dew\Cli\Database;

class InstanceType
{
    const PAY_AS_YOU_GO = 'pay-as-you-go';
    const SUBSCRIPTION = 'subscription';
    const SERVERLESS = 'serverless';

    /**
     * Retrieve all possible values.
     *
     * @return string[]
     */
    public static function all(): array
    {
        return [
            self::PAY_AS_YOU_GO,
            self::SUBSCRIPTION,
            self::SERVERLESS,
        ];
    }
}
