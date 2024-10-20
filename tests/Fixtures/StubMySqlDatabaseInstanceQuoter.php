<?php

declare(strict_types=1);

namespace Dew\Cli\Tests\Fixtures;

use Dew\Cli\Contracts\CommunicatesWithDew;
use Dew\Cli\Contracts\DatabaseStorageRange;
use Dew\Cli\Contracts\InstanceQuotation;
use Dew\Cli\Database\InstanceType;
use Dew\Cli\Database\ManagesSubscriptionTerm;
use Dew\Cli\Database\QuoteDatabaseInstance;
use Dew\Cli\Database\StorageRange;
use Mockery;

final class StubMySqlDatabaseInstanceQuoter extends QuoteDatabaseInstance
{
    use ManagesSubscriptionTerm;

    private string $type;

    public static function makePayAsYouGo(): static
    {
        return (new self(Mockery::mock(CommunicatesWithDew::class), 1))
            ->setType(InstanceType::PAY_AS_YOU_GO);
    }

    public static function makeSubscription(): static
    {
        return (new self(Mockery::mock(CommunicatesWithDew::class), 1))
            ->setType(InstanceType::SUBSCRIPTION);
    }

    public function type(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

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
