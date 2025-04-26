<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Product;

class AdHocAddon implements AddonInterface
{
    use OnDemandRenewalTrait;
    public $id = 0;
    public $name = "";
    public $applyTax = false;
    public $type = "";
    public $overrideOnDemandRenewal;
    protected $parentService;
    const AD_HOC_ADDON_ON_DEMAND_RENEWAL_OPTION = "UndefinedProductAddonOnDemandRenewalOption";
    const AD_HOC_ADDON_ON_DEMAND_RENEWAL_GLOBAL = "global";
    const AD_HOC_ADDON_ON_DEMAND_RENEWAL_PARENT = "parent";
    const AD_HOC_ADDON_ON_DEMAND_RENEWAL_DISABLED = "disabled";
    public static function factory(\WHMCS\Service\Addon $service) : \self
    {
        $adhoc = new AdHocAddon();
        $adhoc->id = $service->id;
        $adhoc->name = $service->name;
        $adhoc->applyTax = $service->applyTax;
        $adhoc->type = "other";
        $adhoc->setParentService($service->getServiceActual());
        return $adhoc;
    }
    public function overrideOnDemandRenewal() : \Illuminate\Database\Eloquent\Relations\HasOne
    {
        throw new \Exception("Method is not supported by Ad-hoc Addon.");
    }
    public function overridingOnDemandRenewal($enable, int $monthly, int $quarterly, int $semiannually, int $annually, int $biennially, int $triennially) : \self
    {
        throw new \Exception("Method is not supported by Ad-hoc Addon.");
    }
    public function resetOnDemandRenewalOverriding()
    {
        throw new \Exception("Method is not supported by Ad-hoc Addon.");
    }
    public function duplicateOverrideOnDemandRenewal($duplicatedModel)
    {
        throw new \Exception("Method is not supported by Ad-hoc Addon.");
    }
    public function setParentService(\WHMCS\Service\Service $parentService) : \self
    {
        $this->parentService = $parentService;
        return $this;
    }
    public function getParentService() : \WHMCS\Service\Service
    {
        return $this->parentService;
    }
    public function getOrderLineItemProductGroupName()
    {
        return "Addons";
    }
    public function getOnDemandRenewalSettings() : OnDemandRenewalSettings
    {
        \WHMCS\Config\Setting::getValue(self::AD_HOC_ADDON_ON_DEMAND_RENEWAL_OPTION);
        switch (\WHMCS\Config\Setting::getValue(self::AD_HOC_ADDON_ON_DEMAND_RENEWAL_OPTION)) {
            case self::AD_HOC_ADDON_ON_DEMAND_RENEWAL_GLOBAL:
                return (new OnDemandRenewalSettings())->populate(NULL);
                break;
            case self::AD_HOC_ADDON_ON_DEMAND_RENEWAL_PARENT:
                if(is_null($this->getParentService())) {
                    throw new \Exception("No Parent Service Found.");
                }
                return $this->getParentService()->getServiceProduct()->getOnDemandRenewalSettings();
                break;
            default:
                return (new OnDemandRenewalSettings())->disable();
        }
    }
}

?>