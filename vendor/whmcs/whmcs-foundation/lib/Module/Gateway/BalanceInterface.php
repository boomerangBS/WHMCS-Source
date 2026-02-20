<?php

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