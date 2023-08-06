<?php

namespace Dew\Cli\Database;

class CreateServerlessDatabaseInstance extends CreateDatabaseInstance
{
    use ManagesServerlessScales;

    /**
     * Indicates whether to enable auto-pause feature.
     */
    public bool $autoPause = false;

    /**
     * Indicates whether to enable force scaling feature.
     */
    public bool $forceScale = false;

    /**
     * Configure auto-pause feature.
     */
    public function autoPause(bool $autoPause = true): self
    {
        $this->autoPause = $autoPause;

        return $this;
    }

    /**
     * Configure scaling policy.
     */
    public function forceScale(bool $force = true): self
    {
        $this->forceScale = $force;

        return $this;
    }

    /**
     * Get the type of database instance.
     */
    public function type(): string
    {
        return InstanceType::SERVERLESS;
    }

    /**
     * Represent as database creation request.
     */
    protected function toAcsRequest(): array
    {
        return array_merge(parent::toAcsRequest(), [
            'scales_min' => $this->scaleMin,
            'scales_max' => $this->scaleMax,
            'auto_pause' => $this->autoPause,
            'force_scale' => $this->forceScale,
        ]);
    }
}