<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\UsageBilling\Invoice\Calculation;

class Included extends Charge
{
    public function __construct($consumed = 0, \WHMCS\Billing\PricingInterface $price = NULL, \WHMCS\UsageBilling\Contracts\Pricing\PriceBracketInterface $bracket = NULL, $isIncluded = true)
    {
        parent::__construct($consumed, NULL, NULL, true);
    }
}

?>