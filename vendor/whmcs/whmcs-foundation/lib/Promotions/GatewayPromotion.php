<?php

namespace WHMCS\Promotions;

class GatewayPromotion extends AbstractPromotion
{
    protected $additionalGateways = [];
    protected $gatewayName = "";
    public function __construct(string $gatewayName, array $additionalGateways = [])
    {
        $this->gatewayName = $gatewayName;
        $this->additionalGateways = $additionalGateways;
    }
    public function isPromotable()
    {
        return !($this->isGatewayActive() || $this->isAdditionalGatewayActive());
    }
    protected function isAdditionalGatewayActive()
    {
        if(empty($this->additionalGateways)) {
            return false;
        }
        return $this->getActiveGateways()->keys()->intersect($this->additionalGateways)->isNotEmpty();
    }
    protected function getActiveGateways() : \WHMCS\Billing\Gateway\Collection
    {
        return \DI::make("WHMCS\\Billing\\Gateway\\PaymentGatewayServiceProvider")->all()->active();
    }
    protected function isGatewayActive()
    {
        return $this->getActiveGateways()->offsetExists($this->getPromotingGatewayName());
    }
    protected function getPromotingGatewayName()
    {
        return $this->gatewayName;
    }
}

?>