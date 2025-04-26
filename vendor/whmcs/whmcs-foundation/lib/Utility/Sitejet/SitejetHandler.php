<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Utility\Sitejet;

class SitejetHandler
{
    const SITEJET_CONFIGURED = "SitejetConfigured";
    public function isSitejetAvailable()
    {
        return 0 < \WHMCS\Product\Server\Adapters\SitejetServerAdapter::getServersWithSitejetEnabled()->count();
    }
    public function setSitejetConfigured() : void
    {
        \WHMCS\Config\Setting::setValue(self::SITEJET_CONFIGURED, "1");
    }
    public function isSitejetConfigured()
    {
        return (bool) \WHMCS\Config\Setting::getValue(self::SITEJET_CONFIGURED);
    }
}

?>