<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\MarketConnect\Promotion;

class UpsellPromotion extends Promotion
{
    protected $supportsUpgrades;
    protected function serviceSupportsUpgrade()
    {
        if(is_null($this->supportsUpgrades)) {
            $service = $this->getUpsellService();
            if($service->isService()) {
                $product = $service->product()->first();
            } else {
                $product = $service->productAddon()->first();
            }
            $promoHelper = \WHMCS\MarketConnect\MarketConnect::factoryPromotionalHelperByProductKey($product->productKey);
            $this->supportsUpgrades = $promoHelper->supportsUpgrades();
        }
        return $this->supportsUpgrades;
    }
    protected function getTargetUrl()
    {
        return $this->serviceSupportsUpgrade() ? routePath("upgrade") : parent::getTargetUrl();
    }
    protected function getInputParameters()
    {
        if($this->serviceSupportsUpgrade()) {
            return ["isproduct" => $this->getUpsellService()->isService(), "serviceid" => $this->getUpsellService()->id];
        }
        if($this->getUpsellService()->isAddon()) {
            $this->upsellService = $this->getUpsellService()->service()->first();
        }
        return parent::getInputParameters();
    }
}

?>