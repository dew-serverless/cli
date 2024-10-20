<?php

declare(strict_types=1);

namespace Dew\Cli\Contracts;

interface DatabaseInstanceQuoter
{
    /**
     * Get available database engine versions.
     *
     * @return string[]
     */
    public function availableEngineVersions(): array;

    /**
     * Get available database deployment options.
     *
     * @return string[]
     */
    public function availableDeploymentOptions(): array;

    /**
     * Get available database instance classes.
     *
     * @return string[]
     */
    public function availableClasses(): array;

    /**
     * Get available database storage types.
     *
     * @return string[]
     */
    public function availableStorageTypes(): array;

    /**
     * Get available database storage range.
     */
    public function availableStorageRange(string $class): DatabaseStorageRange;

    /**
     * Get available database instance deployment zones.
     *
     * @return string[]
     */
    public function availableZones(): array;

    /**
     * Get database instance quotation.
     */
    public function getQuotation(): InstanceQuotation;
}
