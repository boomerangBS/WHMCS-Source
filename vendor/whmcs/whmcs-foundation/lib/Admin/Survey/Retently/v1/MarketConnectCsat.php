<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Admin\Survey\Retently\v1;

class MarketConnectCsat extends GlobalNps
{
    protected $campaignId = "65c90b2f2e01dce615c8ac58";
    const IDENTIFIER_TAG_VALUE = "whmcs-mc-csat-v1";
    const WHITELISTED_PERMISSIONS = ["Manage MarketConnect"];
    public static function shouldRender(string $currentPage)
    {
        return parent::shouldRender($currentPage) && \WHMCS\MarketConnect\MarketConnect::isAccountConfigured();
    }
}

?>