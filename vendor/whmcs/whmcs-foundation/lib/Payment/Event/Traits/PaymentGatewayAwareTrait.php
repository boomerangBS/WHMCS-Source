<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Payment\Event\Traits;

trait PaymentGatewayAwareTrait
{
    private $paymentGateway;
    public function paymentGateway() : \WHMCS\Billing\Gateway\Contract\PaymentGatewayInterface
    {
        return $this->paymentGateway;
    }
    public function setPaymentGateway(\WHMCS\Billing\Gateway\Contract\PaymentGatewayInterface $paymentGateway) : \self
    {
        $this->paymentGateway = $paymentGateway;
        return $this;
    }
    protected function hasPaymentGateway()
    {
        return !is_null($this->paymentGateway);
    }
    protected function assertPaymentGateway() : \self
    {
        if(!$this->hasPaymentGateway()) {
            throw \WHMCS\Payment\Exception\MissingRequirement::ofImplementor("paymentGateway", self::class);
        }
        return $this;
    }
}

?>