<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\UsageBilling\Invoice\Calculation;

class Charge implements \WHMCS\UsageBilling\Contracts\Invoice\UsageCalculationInterface
{
    private $consumed = 0;
    private $bracket;
    private $price;
    private $isIncluded = false;
    public function __construct($consumed = 0, \WHMCS\Billing\PricingInterface $price = NULL, \WHMCS\UsageBilling\Contracts\Pricing\PriceBracketInterface $bracket = NULL, $isIncluded = false)
    {
        if(!is_numeric($consumed) || $consumed < 0) {
            $consumed = 0;
        }
        $this->consumed = $consumed;
        $this->isIncluded = (bool) $isIncluded;
        $this->price = $price;
        $this->bracket = $bracket;
    }
    public function consumed()
    {
        return $this->consumed;
    }
    public function bracket()
    {
        return $this->bracket;
    }
    public function price()
    {
        return $this->price;
    }
    public function isIncluded()
    {
        return $this->isIncluded;
    }
}

?>