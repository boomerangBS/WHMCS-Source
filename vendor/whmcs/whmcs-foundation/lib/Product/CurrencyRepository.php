<?php

namespace WHMCS\Product;

class CurrencyRepository
{
    private $defaultCurrency;
    private $fallbackCurrency;
    const USD_CURRENCY_CODE = "USD";
    public function getUsdCurrency() : \WHMCS\Billing\Currency
    {
        return \WHMCS\Billing\Currency::where("code", self::USD_CURRENCY_CODE)->first();
    }
    public function getDefaultCurrency() : \WHMCS\Billing\Currency
    {
        if(is_null($this->defaultCurrency)) {
            $this->defaultCurrency = \WHMCS\Billing\Currency::defaultCurrency()->first();
        }
        return $this->defaultCurrency;
    }
    public function getFallbackCurrency() : \WHMCS\Billing\Currency
    {
        if(is_null($this->fallbackCurrency)) {
            $this->fallbackCurrency = $this->getUsdCurrency() ?? $this->getDefaultCurrency();
        }
        return $this->fallbackCurrency;
    }
    public function getMoneyAmountsPerCurrency($defaultCurrencyAmount) : array
    {
        $defaultCurrency = $this->getDefaultCurrency();
        $fallbackCurrency = $this->getFallbackCurrency();
        if(is_null($defaultCurrency)) {
            return [];
        }
        $moneyPerCurrencies = [$defaultCurrency->code => round($defaultCurrencyAmount, 2)];
        if($defaultCurrency->isNot($fallbackCurrency)) {
            $moneyPerCurrencies[$fallbackCurrency->code] = $defaultCurrency->convertTo($defaultCurrencyAmount, $fallbackCurrency);
        }
        return $moneyPerCurrencies;
    }
}

?>