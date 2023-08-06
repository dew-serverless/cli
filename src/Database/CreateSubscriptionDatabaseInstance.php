<?php

namespace Dew\Cli\Database;

class CreateSubscriptionDatabaseInstance extends CreateDatabaseInstance
{
    use ManagesSubscriptionTerm;

    /**
     * Get the type of database instance.
     */
    public function type(): string
    {
        return InstanceType::SUBSCRIPTION;
    }

    /**
     * Represent as database creation request.
     */
    protected function toAcsRequest(): array
    {
        return array_merge(parent::toAcsRequest(), [
            'subscription_term' => $this->subscriptionTerm,
            'subscription_type' => $this->subscriptionType,
        ]);
    }
}