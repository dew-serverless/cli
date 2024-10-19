<?php

namespace Dew\Cli\Database;

use Dew\Cli\Contracts\CommunicatesWithDew;
use Dew\Cli\Contracts\DatabaseInstanceQuoter;
use Dew\Cli\Contracts\DatabaseStorageRange;
use Dew\Cli\Contracts\InstanceQuotation as QuotationContract;

abstract class QuoteDatabaseInstance implements DatabaseInstanceQuoter
{
    use ManagesDatabaseInstance, ManagesDatabaseInstanceNetwork;

    public function __construct(
        private CommunicatesWithDew $client,
        private int $projectId
    ) {
        //
    }

    /**
     * Get the database instance type.
     */
    abstract public function type(): string;

    /**
     * Get available database engine versions.
     */
    public function availableEngineVersions(): array
    {
        return collect($this->getAvailableZones())
            ->flatMap->supportedEngines
            ->flatMap->supportedEngineVersions
            ->map->version
            ->unique()
            ->all();
    }

    /**
     * Get available database deployment options.
     */
    public function availableDeploymentOptions(): array
    {
        return collect($this->getAvailableZones())
            ->flatMap->supportedEngines
            ->flatMap->supportedEngineVersions
            ->flatMap->supportedCategorys
            ->map->category
            ->unique()
            ->all();
    }

    /**
     * Get available database storage types.
     */
    public function availableStorageTypes(): array
    {
        return collect($this->getAvailableZones())
            ->flatMap->supportedEngines
            ->flatMap->supportedEngineVersions
            ->flatMap->supportedCategorys
            ->flatMap->supportedStorageTypes
            ->map->storageType
            ->unique()
            ->all();
    }

    /**
     * Get available database instance deployment zones.
     */
    public function availableZones(): array
    {
        return collect($this->getAvailableZones())
            ->map->zoneId
            ->all();
    }

    /**
     * Get available database instance classes.
     */
    public function availableClasses(): array
    {
        return collect($this->getAvailableClasses())
            ->map->DBInstanceClass
            ->all();
    }

    /**
     * Get available database storage range.
     */
    public function availableStorageRange(string $class): DatabaseStorageRange
    {
        $class = collect($this->getAvailableClasses())
            ->first(fn ($item) => $item['DBInstanceClass'] === $class);

        $range = $class['DBInstanceStorageRange'];

        return new StorageRange($range['minValue'], $range['maxValue'], $range['step']);
    }

    /**
     * Get available zones by current setup.
     */
    protected function getAvailableZones(): array
    {
        return $this->client->getAvailableDatabaseZones($this->projectId, [
            'type' => $this->type(),
            'engine' => $this->engine,
            'engine_version' => $this->engineVersion ?? null,
            'deployment' => $this->deployment ?? null,
        ])['data'];
    }

    /**
     * Get available classes by current setup.
     */
    protected function getAvailableClasses(): array
    {
        return $this->client->getAvailableDatabaseClasses($this->projectId, [
            'type' => $this->type(),
            'zone' => $this->zoneId,
            'engine' => $this->engine,
            'engine_version' => $this->engineVersion,
            'deployment' => $this->deployment,
            'storage_type' => $this->storageType,
        ])['data'];
    }

    /**
     * Make a new instance quotation by given response.
     */
    protected function newInstanceQuotation(array $response): InstanceQuotation
    {
        $price = $response['PriceInfo'];

        return new InstanceQuotation(
            $price['Currency'], $price['OriginalPrice'], $price['DiscountPrice'], $price['TradePrice']
        );
    }

    /**
     * Get quotation for the current setup.
     */
    public function getQuotation(): QuotationContract
    {
        $response = $this->requestQuotation();

        $quotation = $this->newInstanceQuotation($response);

        $quotation->setPromotion(
            collect($response['Rules']['Rule'] ?? [])
                ->map(fn ($rule) => new Promotion($rule['RuleId'], $rule['Name'], $rule['Description'] ?? ''))
                ->all()
        );

        $quotation->setCoupons(
            collect($response['PriceInfo']['Coupons']['Coupon'] ?? [])
                ->map(fn ($coupon) => new Coupon($coupon['CouponNo'], $coupon['Name'], $coupon['Description'], $coupon['IsSelected'] === 'true'))
                ->all()
        );

        return $quotation;
    }

    /**
     * Request quotation from API.
     */
    protected function requestQuotation(): array
    {
        return $this->client->getDatabaseQuotation(
            $this->projectId, $this->toQuotationRequest()
        )['data'];
    }

    /**
     * Represent as quotation request.
     */
    protected function toQuotationRequest(): array
    {
        return [
            'type' => $this->type(),
            'engine' => $this->engine,
            'engine_version' => $this->engineVersion,
            'class' => $this->class,
            'storage' => $this->storage,
            'storage_type' => $this->storageType,
        ];
    }
}
