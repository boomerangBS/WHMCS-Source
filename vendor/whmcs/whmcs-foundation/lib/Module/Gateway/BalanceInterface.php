<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Module\Gateway;

interface BalanceInterface extends \JsonSerializable
{
    public function getAmount() : \WHMCS\View\Formatter\Price;
    public function getColor();
    public function getCurrencyCode();
    public function getCurrencyObject() : \WHMCS\Billing\Currency;
    public function getLabel();
    public static function factory($amount, string $currencyCode, string $label, string $color) : BalanceInterface;
}

?>