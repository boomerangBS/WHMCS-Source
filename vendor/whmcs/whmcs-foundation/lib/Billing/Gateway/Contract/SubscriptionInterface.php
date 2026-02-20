<?php

namespace WHMCS\Billing\Gateway\Contract;

interface SubscriptionInterface
{
    public function isSubscriptionCapable();
    public function cancelSubscription($subscriptionId) : array;
}

?>