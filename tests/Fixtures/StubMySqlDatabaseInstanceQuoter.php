<?php

namespace Dew\Cli\Tests\Fixtures;

use Dew\Cli\Contracts\DatabaseInstanceQuoter;
use Dew\Cli\Contracts\DatabaseStorageRange;
use Dew\Cli\Contracts\InstanceQuotation;
use Dew\Cli\Database\ManagesDatabaseInstanceNetwork;
use Dew\Cli\Database\ManagesDatabaseInstance;
use Dew\Cli\Database\ManagesSubscriptionTerm;
use Dew\Cli\Database\StorageRange;

class StubMySqlDatabaseInstanceQuoter implements DatabaseInstanceQuoter
{
    use ManagesDatabaseInstance, ManagesDatabaseInstanceNetwork;
    use ManagesSubscriptionTerm;

    public function availableEngineVersions(): array
    {
        return ['8.0'];
    }

    public function availableDeploymentOptions(): array
    {
        return ['Basic'];
    }

    public function availableClasses(): array
    {
        return ['mysql.n2.medium.1'];
    }

    public function availableStorageTypes(): array
    {
        return ['cloud_essd'];
    }

    public function availableStorageRange(string $class): DatabaseStorageRange
    {
        return new StorageRange(20, 2000, 5);
    }

    public function availableZones(): array
    {
        return ['us-west-1a'];
    }

    public function getQuotation(): InstanceQuotation
    {
        return new StubDatabaseInstanceQuotation;
    }
}