<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
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