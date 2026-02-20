<?php

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