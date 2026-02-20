<?php

namespace WHMCS\Billing;

class CurrencyData implements CurrencyInterface
{
    private $code = "";
    private $rate = 0;
    public function getCode()
    {
        return $this->code;
    }
    public function getRate()
    {
        return $this->rate;
    }
    public function setCode(string $code)
    {
        $this->code = $code;
        return $this;
    }
    public function setRate($rate)
    {
        $this->rate = $rate;
        return $this;
    }
}

?>