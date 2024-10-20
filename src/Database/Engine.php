<?php

declare(strict_types=1);

namespace Dew\Cli\Database;

class Engine
{
    const MYSQL = 'mysql';
    const MARIADB = 'mariadb';
    const POSTGRESQL = 'postgresql';
    const SQLSERVER = 'sqlserver';

    /**
     * Retrieve all possible values.
     *
     * @return string[]
     */
    public static function all(): array
    {
        return [
            self::MYSQL,
            self::MARIADB,
            self::POSTGRESQL,
            self::SQLSERVER,
        ];
    }
}
