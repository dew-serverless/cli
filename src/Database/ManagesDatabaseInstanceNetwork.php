<?php

declare(strict_types=1);

namespace Dew\Cli\Database;

trait ManagesDatabaseInstanceNetwork
{
    /**
     * Database instance deployment zone.
     */
    public string $zoneId;

    /**
     * Configure instance deployment zone.
     */
    public function zone(string $zoneId): self
    {
        $this->zoneId = $zoneId;

        return $this;
    }
}
