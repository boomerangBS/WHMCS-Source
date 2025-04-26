<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Module\Gateway;

trait CurrencyObjectTrait
{
    public function getCurrencyObject() : \WHMCS\Billing\Currency
    {
        if(empty($currencyCache[$this->getCurrencyCode()])) {
            try {
                $currencyCache[$this->getCurrencyCode()] = \WHMCS\Billing\Currency::where("code", $this->getCurrencyCode())->firstOrFail();
            } catch (\Throwable $t) {
                return NULL;
            }
        }
        return $currencyCache[$this->getCurrencyCode()];
    }
}

?>