<?php

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