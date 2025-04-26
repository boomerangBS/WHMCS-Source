<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\UsageBilling\Contracts\Invoice;

interface UsageCalculationInterface
{
    public function consumed();
    public function bracket();
    public function price();
    public function isIncluded();
}

?>