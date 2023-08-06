<?php

namespace Dew\Cli\Database;

trait ManagesSubscriptionTerm
{
    /**
     * The subscription term.
     */
    public int $subscriptionTerm;

    /**
     * The subscription type.
     */
    public string $subscriptionType;

    /**
     * Configure subscription term.
     */
    public function subscribeFor(int $term, string $type): self
    {
        $this->subscriptionTerm = $term;
        $this->subscriptionType = $type;

        return $this;
    }
}