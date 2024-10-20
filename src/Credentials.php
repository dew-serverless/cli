<?php

declare(strict_types=1);

namespace Dew\Cli;

class Credentials
{
    public function __construct(
        protected string $keyId,
        protected string $keySecret,
        protected string $accountId
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

    public function accountId(): string
    {
        return $this->accountId;
    }
}
