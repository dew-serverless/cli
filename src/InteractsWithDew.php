<?php

namespace Dew\Cli;

trait InteractsWithDew
{
    /**
     * The token to communicate with Dew.
     */
    public string $token;

    /**
     * Configure Dew access token.
     */
    public function tokenUsing(string $token): self
    {
        $this->token = $token;

        return $this;
    }
}