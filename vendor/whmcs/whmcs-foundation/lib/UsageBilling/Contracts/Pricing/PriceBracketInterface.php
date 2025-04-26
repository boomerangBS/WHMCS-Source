<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\UsageBilling\Contracts\Pricing;

interface PriceBracketInterface
{
    public function schemaType();
    public function withinRange($value, $unitType);
    public function belowRange($value, $unitType);
    public function pricing();
    public function isFree();
    public function pricingForCurrencyId($id);
    public function newCollection();
    public function relationEntity();
}

?>