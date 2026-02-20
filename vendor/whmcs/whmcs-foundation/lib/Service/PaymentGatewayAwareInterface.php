<?php

namespace WHMCS\Service;

interface PaymentGatewayAwareInterface
{
    public function getPaymentGatewayIdentifier();
    public function addPaymentGatewayIdentifier($paymentGatewayIdentifier) : void;
    public function removePaymentGatewayIdentifier() : void;
    public function billingPaymentGateway() : \WHMCS\Billing\Gateway\PaymentGateway;
}

?>