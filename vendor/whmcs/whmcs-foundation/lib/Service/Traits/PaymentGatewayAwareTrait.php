<?php

namespace WHMCS\Service\Traits;

trait PaymentGatewayAwareTrait
{
    private function overseePaymentGatewayIdentifier($value)
    {
        throw new \BadMethodCallException("not implemented");
    }
    public function getPaymentGatewayIdentifier()
    {
        return $this->overseePaymentGatewayIdentifier();
    }
    public function removePaymentGatewayIdentifier() : void
    {
        $this->overseePaymentGatewayIdentifier("");
    }
    public function addPaymentGatewayIdentifier($paymentGatewayIdentifier) : void
    {
        $this->overseePaymentGatewayIdentifier($paymentGatewayIdentifier);
    }
    public function billingPaymentGateway() : \WHMCS\Billing\Gateway\PaymentGateway
    {
        return \DI::make("WHMCS\\Billing\\Gateway\\PaymentGatewayServiceProvider")->all()->get($this->getPaymentGatewayIdentifier());
    }
}

?>