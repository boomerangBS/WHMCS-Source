<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\UsageBilling\Metrics\Units;

class Accounts extends WholeNumber
{
    public function __construct($name = "Accounts", $singlePerUnitName = "Account", $pluralPerUnitName = "Accounts", $prefix = NULL, $suffix = "")
    {
        parent::__construct($name, $singlePerUnitName, $pluralPerUnitName, $prefix, $suffix);
    }
}

?>