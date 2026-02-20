<?php

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