<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\MarketConnect\Promotion\Service;

class SitelockVPN extends AbstractService
{
    protected $name = \WHMCS\MarketConnect\MarketConnect::SERVICE_SITELOCKVPN;
    protected $friendlyName = "Sitelock VPN";
    protected $primaryIcon = "assets/img/marketconnect/sitelockvpn/logo.png";
    protected $promosRequireQualifyingProducts = false;
    protected $requiresDomain = false;
    protected $productKeys;
    protected $qualifyingProductTypes = [];
    protected $loginPanel = ["label" => "marketConnect.sitelockvpn.manageVPN", "icon" => "fa-network-wired", "image" => "assets/img/marketconnect/sitelockvpn/logo-sml.png", "color" => "pomegranate", "dropdownReplacementText" => "sitelockvpn.loginPanelText"];
    protected $defaultPromotionalContent;
    protected $planFeatures;
    const SITELOCKVPN_STANDARD = NULL;
    public function getPlanFeatures($key)
    {
        return isset($this->planFeatures[$key]) ? $this->planFeatures[$key] : [];
    }
}

?>