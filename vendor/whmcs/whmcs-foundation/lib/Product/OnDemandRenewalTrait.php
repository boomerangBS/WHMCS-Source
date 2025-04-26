<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Product;

trait OnDemandRenewalTrait
{
    public function getOnDemandRenewalSettings() : OnDemandRenewalSettings
    {
        return (new OnDemandRenewalSettings())->populate($this->overrideOnDemandRenewal);
    }
    public function doOverridingOnDemandRenewal($enable, int $monthly, int $quarterly, int $semiannually, int $annually, int $biennially, int $triennially) : \self
    {
        $this->overrideOnDemandRenewal->enabled = $enable;
        $this->overrideOnDemandRenewal->monthly = $monthly;
        $this->overrideOnDemandRenewal->quarterly = $quarterly;
        $this->overrideOnDemandRenewal->semiannually = $semiannually;
        $this->overrideOnDemandRenewal->annually = $annually;
        $this->overrideOnDemandRenewal->biennially = $biennially;
        $this->overrideOnDemandRenewal->triennially = $triennially;
        $this->overrideOnDemandRenewal->save();
        return $this;
    }
    public function resetOnDemandRenewalOverriding()
    {
        if(!is_null($this->overrideOnDemandRenewal)) {
            $this->overrideOnDemandRenewal->delete();
        }
    }
    public function duplicateOverrideOnDemandRenewal(\WHMCS\Model\AbstractModel $duplicatedModel) : \WHMCS\Model\AbstractModel
    {
        if(!is_null($this->overrideOnDemandRenewal)) {
            $duplicate = $this->overrideOnDemandRenewal->replicate();
            $duplicate->relId = $duplicatedModel->id;
            $duplicate->save();
        }
        return $duplicatedModel;
    }
    public function getTypeForDisplay()
    {
        $type = $this->type;
        switch ($type) {
            case "hostingaccount":
                return "Hosting Account";
                break;
            case "reselleraccount":
                return "Reseller Account";
                break;
            case "server":
                return "Dedicated/VPS Server";
                break;
            case "other":
                return "Other Product/Service";
                break;
            default:
                return $type;
        }
    }
}

?>