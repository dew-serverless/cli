<?php

declare(strict_types=1);

namespace Dew\Cli\Database;

class QuoteSubscriptionDatabaseInstance extends QuoteDatabaseInstance
{
    use ManagesSubscriptionTerm;

    /**
     * Get the database instance type.
     */
    public function type(): string
    {
        return InstanceType::SUBSCRIPTION;
    }

    /**
     * Get the quotation request payload.
     *
     * @return array<string, mixed>
     */
    protected function toQuotationRequest(): array
    {
        return array_merge(parent::toQuotationRequest(), [
            'subscription_term' => $this->subscriptionTerm,
            'subscription_type' => $this->subscriptionType,
        ]);
    }
}
