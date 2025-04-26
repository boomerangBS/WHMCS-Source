<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Product;

class OnDemandRenewalSettings
{
    protected $isOverridden = false;
    protected $isEnabled = false;
    protected $monthly = 0;
    protected $quarterly = 0;
    protected $semiannually = 0;
    protected $annually = 0;
    protected $biennially = 0;
    protected $triennially = 0;
    public function populate(OnDemandRenewal $onDemandRenewal) : \self
    {
        $this->isEnabled = (bool) \WHMCS\Config\Setting::getValue("OnDemandRenewalsEnabled");
        $this->monthly = \WHMCS\Config\Setting::getValue("OnDemandRenewalPeriodMonthly");
        $this->quarterly = \WHMCS\Config\Setting::getValue("OnDemandRenewalPeriodQuarterly");
        $this->semiannually = \WHMCS\Config\Setting::getValue("OnDemandRenewalPeriodSemiAnnually");
        $this->annually = \WHMCS\Config\Setting::getValue("OnDemandRenewalPeriodAnnually");
        $this->biennially = \WHMCS\Config\Setting::getValue("OnDemandRenewalPeriodBiennially");
        $this->triennially = \WHMCS\Config\Setting::getValue("OnDemandRenewalPeriodTriennially");
        if(!is_null($onDemandRenewal)) {
            $this->isOverridden = true;
            $this->isEnabled = $onDemandRenewal->enabled;
            $this->monthly = $onDemandRenewal->monthly;
            $this->quarterly = $onDemandRenewal->quarterly;
            $this->semiannually = $onDemandRenewal->semiannually;
            $this->annually = $onDemandRenewal->annually;
            $this->biennially = $onDemandRenewal->biennially;
            $this->triennially = $onDemandRenewal->triennially;
        }
        return $this;
    }
    public function isEnabled()
    {
        return $this->isEnabled;
    }
    public function disable() : \self
    {
        $this->isEnabled = false;
        return $this;
    }
    public function isOverridden()
    {
        return $this->isOverridden;
    }
    public function getMonthly() : int
    {
        return $this->monthly;
    }
    public function getQuarterly() : int
    {
        return $this->quarterly;
    }
    public function getSemiAnnually() : int
    {
        return $this->semiannually;
    }
    public function getAnnually() : int
    {
        return $this->annually;
    }
    public function getBiennially() : int
    {
        return $this->biennially;
    }
    public function getTriennially() : int
    {
        return $this->triennially;
    }
    public function getPeriodByBillingCycle($billingCycle) : int
    {
        strtolower($billingCycle);
        switch (strtolower($billingCycle)) {
            case "monthly":
                return $this->getMonthly();
                break;
            case "quarterly":
                return $this->getQuarterly();
                break;
            case "semi-annually":
                return $this->getSemiAnnually();
                break;
            case "annually":
                return $this->getAnnually();
                break;
            case "biennially":
                return $this->getBiennially();
                break;
            case "triennially":
                return $this->getTriennially();
                break;
            default:
                throw new \Exception("Billing cycle is not supported.");
        }
    }
}

?>