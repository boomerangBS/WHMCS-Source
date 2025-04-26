<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

namespace WHMCS;

// Decoded file for php version 72.
class AddonPricing extends Pricing
{
    protected function getCycleData($cycle = 0, int $months) : array
    {
        return $this->getCycleBaseData($cycle, $months);
    }
}

?>