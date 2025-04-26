<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\UsageBilling\Metrics\Units;

class GigaBytes extends Bytes
{
    public function __construct($name = "Gigabytes", $singlePerUnitName = NULL, $pluralPerUnitName = NULL, $prefix = NULL, $suffix = "GB")
    {
        parent::__construct($name, $singlePerUnitName, $pluralPerUnitName, $prefix, $suffix);
    }
}

?>