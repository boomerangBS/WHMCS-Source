<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\MarketConnect\Services;

class ClientAreaOutputParameters
{
    protected $params;
    public function __construct(array $params)
    {
        $this->params = $params;
    }
    public function isActiveOrder()
    {
        $orderNumber = marketconnect_GetOrderNumber($this->params);
        return $orderNumber && $this->params["status"] == "Active";
    }
    public function getServiceId() : int
    {
        return (int) $this->params["serviceid"];
    }
    public function getAddonId() : int
    {
        return array_key_exists("addonId", $this->params) ? (int) $this->params["addonId"] : 0;
    }
    public function isProduct()
    {
        return $this->getAddonId() == 0;
    }
    public function isAddon()
    {
        return 0 < $this->getAddonId();
    }
    public function getUpgradeServiceId() : int
    {
        return $this->isAddon() ? $this->getAddonId() : $this->getServiceId();
    }
    public function getModel() : \WHMCS\Model\AbstractModel
    {
        return $this->params["model"];
    }
}

?>