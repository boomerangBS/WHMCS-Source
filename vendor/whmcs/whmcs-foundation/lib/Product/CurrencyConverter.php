<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Product;

class CurrencyConverter
{
    protected $currencyId;
    public function __construct(int $currencyId)
    {
        $this->currencyId = $currencyId;
    }
    public function convert($amount, int $fromCurrencyId) : int
    {
        return convertCurrency($amount, $fromCurrencyId, $this->currencyId);
    }
}

?>