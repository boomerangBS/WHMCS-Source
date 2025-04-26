<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Service\Traits;

trait SubscriptionAwareTrait
{
    use PaymentGatewayAwareTrait;
    private function overseeSubscriptionIdentifier($value)
    {
        throw new \BadMethodCallException("not implemented");
    }
    public function getSubscriptionId()
    {
        return $this->overseeSubscriptionIdentifier();
    }
    public function removeSubscriptionId() : void
    {
        $this->overseeSubscriptionIdentifier("");
    }
    public function addSubscriptionId($subscriptionId) : void
    {
        $this->overseeSubscriptionIdentifier($subscriptionId);
    }
    public function cancelSubscription() : array
    {
        try {
            $result = $this->cancelPaymentGatewaySubscription();
            if($result["status"] == "success") {
                $this->removeSubscriptionId();
            }
            return $result;
        } catch (\LogicException $e) {
        } catch (\RuntimeException $e) {
        }
    }
    public function cancelPaymentGatewaySubscription() : array
    {
        if($this->getPaymentGatewayIdentifier() == "") {
            throw new \LogicException("gateway undefined");
        }
        if($this->getSubscriptionId() == "") {
            throw new \LogicException("subscription identifier undefined");
        }
        $paymentGateway = $this->billingPaymentGateway();
        if(is_null($paymentGateway)) {
            throw new \RuntimeException("gateway unknown");
        }
        if(!$paymentGateway->isAvailable()) {
            throw new \RuntimeException("gateway unavailable");
        }
        if(!$paymentGateway->isSubscriptionCapable()) {
            throw new \RuntimeException("gateway lacks subscription management");
        }
        return $paymentGateway->cancelSubscription($this->getSubscriptionId());
    }
}

?>