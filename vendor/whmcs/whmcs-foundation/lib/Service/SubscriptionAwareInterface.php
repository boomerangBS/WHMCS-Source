<?php

namespace WHMCS\Service;

interface SubscriptionAwareInterface extends PaymentGatewayAwareInterface
{
    public function getSubscriptionId();
    public function removeSubscriptionId() : void;
    public function addSubscriptionId($subscriptionId) : void;
    public function cancelSubscription() : array;
    public function cancelPaymentGatewaySubscription() : array;
}

?>