<?php

namespace WHMCS\Payment\Contracts;

interface MonetaryAmountInterface
{
    public function currency() : \WHMCS\Billing\Currency;
    public function value();
    public function rawValue();
}

?>