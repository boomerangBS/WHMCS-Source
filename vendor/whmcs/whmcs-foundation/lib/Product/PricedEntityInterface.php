<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Product;

interface PricedEntityInterface
{
    public function isFree();
    public function isOneTime();
    public function getAvailableBillingCycles() : array;
    public function pricing($currency) : Pricing;
}

?>