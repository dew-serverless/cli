<?php

namespace Dew\Cli;

class Project
{
    protected Credentials $credentials;

    public function __construct(
        protected string $name,
        protected string $region
    ) {
        //
    }

    public function credentials()
    {
        return $this->credentials;
    }

    public function credentialsUsing(Credentials $credentials)
    {
        $this->credentials = $credentials;

        return $this;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function region(): string
    {
        return $this->region;
    }

    public function deploymentBucket(): string
    {
        return sprintf('%s-deployments', $this->name());
    }

    public function serviceName()
    {
        return $this->name();
    }
}