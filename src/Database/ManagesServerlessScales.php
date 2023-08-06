<?php

namespace Dew\Cli\Database;

trait ManagesServerlessScales
{
    /**
     * The minimum RDS Capacity Unit for scaling down.
     */
    public float|int $scaleMin;

    /**
     * The maximum RDS Capacity Unit for scaling up.
     */
    public int $scaleMax;

    /**
     * Configure serverless scaling range.
     */
    public function scales(float|int $min, int $max): self
    {
        $this->scaleMin = $min;
        $this->scaleMax = $max;

        return $this;
    }
}