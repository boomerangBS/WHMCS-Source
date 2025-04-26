<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Billing;

interface PricingInterface
{
    const TYPE_PRODUCT = "product";
    const TYPE_ADDON = "addon";
    const TYPE_CONFIGOPTION = "configoptions";
    const TYPE_DOMAIN_REGISTER = "domainregister";
    const TYPE_DOMAIN_TRANSFER = "domaintransfer";
    const TYPE_DOMAIN_RENEW = "domainrenew";
    const TYPE_DOMAIN_ADDON = "domainaddons";
    const TYPE_USAGE = "usage";
    public function pricingType();
}

?>