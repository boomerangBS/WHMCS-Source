<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Billing\Invoice;

class Helper
{
    public static function convertCurrency($amount, \WHMCS\Billing\Currency $currency, \WHMCS\Billing\Invoice $invoice)
    {
        $userCurrency = $invoice->client->currencyrel;
        if($userCurrency->id != $currency->id) {
            $amount = convertCurrency($amount, $currency->id, $userCurrency->id);
            if($invoice->total < $amount + 1 && $amount - 1 < $invoice->total) {
                $amount = $invoice->total;
            }
        }
        return $amount;
    }
}

?>