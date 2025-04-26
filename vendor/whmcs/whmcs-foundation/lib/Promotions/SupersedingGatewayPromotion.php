<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Promotions;

class SupersedingGatewayPromotion extends GatewayPromotion
{
    public function isPromotable()
    {
        return parent::isPromotable() && $this->hasSupersedeGateways();
    }
    protected function hasSupersedeGateways()
    {
        return $this->supersededGateways()->isNotEmpty();
    }
    protected function supersededGateways() : \WHMCS\Billing\Gateway\Collection
    {
        return $this->getActiveGateways()->obsolete()->filter(function (\WHMCS\Billing\Gateway\Contract\PaymentGatewayInterface $obsoletePaymentGateway) {
            return $obsoletePaymentGateway->supersededBy()->has($this->getPromotingGatewayName());
        });
    }
}

?>