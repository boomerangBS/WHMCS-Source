<?php

namespace WHMCS\Billing\Gateway\Contract;

interface CurrencyInterface
{
    public function isSupportedCurrency(\WHMCS\Billing\Currency $currency) : \WHMCS\Billing\Currency;
}

?>