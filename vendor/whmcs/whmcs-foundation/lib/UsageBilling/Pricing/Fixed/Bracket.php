<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\UsageBilling\Pricing\Fixed;

class Bracket extends \WHMCS\UsageBilling\Pricing\AbstractPriceBracket
{
    use \WHMCS\Model\HasServiceEntityTrait;
    protected $table = "tblpricing_fixed_bracket";
    public function getPricingMorphClassname()
    {
        return "WHMCS\\UsageBilling\\Pricing\\Fixed\\Pricing";
    }
    public function servicePricing($service)
    {
        if($service instanceof \WHMCS\Service\ConfigOption) {
            $service = $service->service;
        }
        $currency = $service->client->currencyrel;
        $billingCycle = $service->billingCycle;
        $pricing = $this->pricing()->where("currency", $currency->id)->where($billingCycle, ">", 0)->first();
        if($pricing) {
            $pricing->setRelation("bracket", $this);
        }
        return $pricing;
    }
}

?>