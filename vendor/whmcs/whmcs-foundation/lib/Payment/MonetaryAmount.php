<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Payment;

class MonetaryAmount implements Contracts\MonetaryAmountInterface
{
    private $currency;
    private $currencyAlignedValue;
    private $rawValue;
    public function __construct(\WHMCS\Billing\Currency $currency, $value)
    {
        $this->currency = $currency;
        $this->rawValue = $value;
        $this->currencyAlignedValue = $currency->valueInCurrencyPrecision($value);
    }
    public function currency() : \WHMCS\Billing\Currency
    {
        return $this->currency;
    }
    public function value()
    {
        return $this->currencyAlignedValue;
    }
    public function rawValue()
    {
        return $this->rawValue;
    }
}

?>