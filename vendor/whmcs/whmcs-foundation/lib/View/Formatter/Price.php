<?php

namespace WHMCS\View\Formatter;

class Price
{
    protected $price = 0;
    protected $currency;
    protected $defaultFormat;
    protected $defaultCurrencyDescriptor = ["format" => "1", "prefix" => "", "suffix" => ""];
    const PREFIX = "{PREFIX}";
    const PRICE = "{PRICE}";
    const SUFFIX = "{SUFFIX}";
    public function __construct($price, $currency = NULL)
    {
        $this->price = $price;
        $this->currency = $currency;
    }
    public function getValue()
    {
        return (double) $this->price;
    }
    public function __toString()
    {
        return $this->toFull();
    }
    public function toFull()
    {
        return $this->format(self::PREFIX . self::PRICE . self::SUFFIX);
    }
    public function toPrefixed()
    {
        return $this->format(self::PREFIX . self::PRICE);
    }
    public function toSuffixed()
    {
        return $this->format(self::PRICE . self::SUFFIX);
    }
    public function toNumeric()
    {
        return $this->format(self::PRICE, ["format" => 1]);
    }
    public function format($format = NULL, $currency = NULL)
    {
        if(is_null($format)) {
            $format = $this->defaultFormat;
        }
        if(is_null($currency)) {
            $currency = $this->currency;
        }
        if(!is_array($currency) && !$currency instanceof \WHMCS\Billing\Currency) {
            $currency = $this->defaultCurrencyDescriptor;
        } else {
            foreach ($this->defaultCurrencyDescriptor as $key => $value) {
                if(!isset($currency[$key])) {
                    $currency[$key] = $value;
                }
            }
        }
        $format_dm = "2";
        $format_dp = ".";
        $format_ts = "";
        if($currency["format"] == 2) {
            $format_dm = "2";
            $format_dp = ".";
            $format_ts = ",";
        } elseif($currency["format"] == 3) {
            $format_dm = "2";
            $format_dp = ",";
            $format_ts = ".";
        } elseif($currency["format"] == 4) {
            $format_dm = "0";
            $format_dp = "";
            $format_ts = ",";
        }
        $formattedAmount = number_format($this->price, $format_dm, $format_dp, $format_ts);
        $format = str_replace(self::PREFIX, $currency["prefix"], $format);
        $format = str_replace(self::PRICE, $formattedAmount, $format);
        $format = str_replace(self::SUFFIX, $currency["suffix"], $format);
        return $format;
    }
    public function getCurrency()
    {
        return $this->currency;
    }
    public static function adjustDecimals($amount, $currencyCode)
    {
        if(is_numeric($amount)) {
            $currency = (new \WHMCS\Billing\Currency())->setCode($currencyCode);
            if(in_array($currencyCode, $currency->nonFractionalCurrencyCodes())) {
                $amount = (int) $currency->valueInCurrencyPrecision((double) $amount);
            }
        }
        return $amount;
    }
    public function isCycleDisabled()
    {
        return $this->price < 0;
    }
}

?>