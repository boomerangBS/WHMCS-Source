<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Service\Adapters;

trait SitejetProductAwareTrait
{
    protected static $siteJetProductAddons = [];
    protected function getAdminPredefinedAddonsForModule() : \Illuminate\Support\Collection
    {
        $hookResults = run_hook("AdminPredefinedAddons", []);
        $predefinedAddons = [];
        foreach ($hookResults as $hookResult) {
            $predefinedAddons = array_merge($predefinedAddons, $hookResult);
        }
        $predefinedAddons = collect($predefinedAddons);
        return $predefinedAddons->filter(function ($value) {
            return $value["module"] === $this->getProduct()->module;
        });
    }
    protected function getSitejetAddonProductKey()
    {
        $predefinedAddons = $this->getAdminPredefinedAddonsForModule();
        $sitejetAddon = $predefinedAddons->first(function ($value) {
            return $value["tag"] === "sitejet";
        });
        return $sitejetAddon["featureaddon"] ?? NULL;
    }
    protected function loadSiteJetAddons() : void
    {
        $sitejetAddonProductKey = $this->getSitejetAddonProductKey();
        if(is_null($sitejetAddonProductKey)) {
            return NULL;
        }
        if(!isset(self::$siteJetProductAddons[$sitejetAddonProductKey])) {
            $siteJetEntityIds = \WHMCS\Config\Module\ModuleConfiguration::where("entity_type", "addon")->where("setting_name", "configoption1")->where("value", $sitejetAddonProductKey)->pluck("entity_id")->toArray();
            self::$siteJetProductAddons[$sitejetAddonProductKey] = \WHMCS\Product\Addon::whereIn("id", $siteJetEntityIds)->get();
        }
    }
    public function getAvailableSitejetProductAddons() : \Illuminate\Support\Collection
    {
        $product = $this->getProduct();
        $availableAddons = [];
        $this->loadSiteJetAddons();
        $sitejetAddonProductKey = $this->getSitejetAddonProductKey();
        if(is_null($sitejetAddonProductKey)) {
            return collect([]);
        }
        if(!empty(self::$siteJetProductAddons[$sitejetAddonProductKey])) {
            foreach (self::$siteJetProductAddons[$sitejetAddonProductKey] as $productAddon) {
                if(in_array($product->id, $productAddon->packages)) {
                    $availableAddons[] = $productAddon;
                }
            }
        }
        return collect($availableAddons);
    }
}

?>