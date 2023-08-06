<?php

namespace Dew\Cli\Contracts;

interface DatabaseInstanceQuoter
{
    /**
     * Get available database engine versions.
     */
    public function availableEngineVersions(): array;

    /**
     * Get available database deployment options.
     */
    public function availableDeploymentOptions(): array;

    /**
     * Get available database instance classes.
     */
    public function availableClasses(): array;

    /**
     * Get available database storage types.
     */
    public function availableStorageTypes(): array;

    /**
     * Get available database storage range.
     */
    public function availableStorageRange(string $class): DatabaseStorageRange;

    /**
     * Get available database instance deployment zones.
     */
    public function availableZones(): array;

    /**
     * Get database instance quotation.
     */
    public function getQuotation(): InstanceQuotation;
}