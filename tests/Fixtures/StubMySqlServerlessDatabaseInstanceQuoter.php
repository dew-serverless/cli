<?php

declare(strict_types=1);

namespace Dew\Cli\Tests\Fixtures;

use Dew\Cli\Contracts\CommunicatesWithDew;
use Dew\Cli\Contracts\DatabaseStorageRange;
use Dew\Cli\Contracts\InstanceQuotation;
use Dew\Cli\Database\InstanceType;
use Dew\Cli\Database\ManagesServerlessScales;
use Dew\Cli\Database\QuoteDatabaseInstance;
use Dew\Cli\Database\StorageRange;
use Mockery;

final class StubMySqlServerlessDatabaseInstanceQuoter extends QuoteDatabaseInstance
{
    use ManagesServerlessScales;

    public static function make(): static
    {
        return new self(Mockery::mock(CommunicatesWithDew::class), 1);
    }

    public function type(): string
    {
        return InstanceType::SERVERLESS;
    }

    public function availableEngineVersions(): array
    {
        return ['8.0'];
    }

    public function availableDeploymentOptions(): array
    {
        return ['serverless_basic'];
    }

    public function availableClasses(): array
    {
        return ['mysql.n2.serverless.1c'];
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
        return new StubServerlessDatabaseInstanceQuotation;
    }
}
