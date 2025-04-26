<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\UsageBilling\Metrics\Units;

class FloatingPoint extends AbstractUnit
{
    public function type()
    {
        return \WHMCS\UsageBilling\Contracts\Metrics\UnitInterface::TYPE_FLOAT_PRECISION_LOW;
    }
}

?>