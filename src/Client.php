<?php

namespace Dew\Cli;

use GuzzleHttp\Client as BaseClient;

class Client
{
    /**
     * Create a new instance of client.
     */
    public static function make(): BaseClient
    {
        return new BaseClient([
            'base_uri' => getenv('DEW_ENDPOINT'),
        ]);
    }
}