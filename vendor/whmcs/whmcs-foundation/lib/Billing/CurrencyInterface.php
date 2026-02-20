<?php

namespace WHMCS\Billing;

interface CurrencyInterface
{
    public function getCode();
    public function setCode(string $code);
    public function getRate();
    public function setRate($rate);
}

?>