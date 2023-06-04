<?php

namespace Dew\Cli;

class Credentials
{
    public function __construct(
        protected string $keyId,
        protected string $keySecret
    ) {
        //
    }

    public function keyId(): string
    {
        return $this->keyId;
    }

    public function keySecret(): string
    {
        return $this->keySecret;
    }
}