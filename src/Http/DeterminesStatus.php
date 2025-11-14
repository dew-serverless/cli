<?php

declare(strict_types=1);

namespace Dew\Cli\Http;

trait DeterminesStatus
{
    /**
     * Determines if the status code is 401 Unauthorized.
     */
    public function unauthorized(): bool
    {
        return $this->status() === 401;
    }

    /**
     * Determines if the status code is an error.
     */
    public function error(): bool
    {
        if ($this->clientError()) {
            return true;
        }

        return (bool) $this->serverError();
    }

    /**
     * Determines if the status code is a client error.
     */
    public function clientError(): bool
    {
        return $this->status() >= 400 && $this->status() < 500;
    }

    /**
     * Determines if the status code is a server error.
     */
    public function serverError(): bool
    {
        return $this->status() >= 500 && $this->status() < 600;
    }
}
