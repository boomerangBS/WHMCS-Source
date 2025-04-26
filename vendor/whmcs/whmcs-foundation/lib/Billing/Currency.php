<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Billing;

class Currency extends \WHMCS\Model\AbstractModel implements CurrencyInterface
{
    protected $table = "tblcurrencies";
    public $timestamps = false;
    protected $fillable = ["rate"];
    const DEFAULT_CURRENCY_ID = 1;
    public static function boot()
    {
        parent::boot();
        static::observe("WHMCS\\Billing\\Observers\\CurrencyObserver");
    }
    public function scopeDefaultCurrency($query)
    {
        return $query->where("default", 1);
    }
    public function scopeCode(\Illuminate\Database\Eloquent\Builder $query, string $code) : \Illuminate\Database\Eloquent\Builder
    {
        return $query->where("code", $code);
    }
    public function scopeDefaultSorting($query)
    {
        return $query->orderBy("default", "desc")->orderBy("code");
    }
    public static function validateCurrencyCode(&$currencyCode)
    {
        $currencyCode = strtoupper(trim($currencyCode));
        return (bool) preg_match("/^[A-Z]{2,4}\$/", $currencyCode);
    }
    public static function factoryForClientArea()
    {
        $currencyId = \Auth::client() ? \Auth::client()->currencyId : \WHMCS\Session::get("currency");
        if(!$currencyId) {
            try {
                $currencyModel = self::defaultCurrency()->firstOrFail();
                return $currencyModel;
            } catch (\Throwable $e) {
                $currencyId = self::DEFAULT_CURRENCY_ID;
            }
        }
        return self::find((int) $currencyId);
    }
    public function convertTo($amount, $currency) : Currency
    {
        return self::convertBetween($this, $amount, $currency);
    }
    public static function convertBetween(CurrencyInterface $fromCurrency, $amount, $toCurrency) : CurrencyInterface
    {
        $amount /= $fromCurrency->getRate();
        $amount *= $toCurrency->getRate();
        return (double) format_as_currency($amount);
    }
    public function getCode()
    {
        return $this->code;
    }
    public function setCode($code) : \self
    {
        $this->code = $code;
        return $this;
    }
    public function getRate()
    {
        return $this->rate;
    }
    public function setRate($rate) : \self
    {
        $this->rate = $rate;
        return $this;
    }
    public function nonFractionalCurrencyCodes() : array
    {
        return ["BIF", "BYR", "CLP", "DJF", "GNF", "HUF", "ISK", "JPY", "KMF", "KRW", "MGA", "PYG", "RWF", "UGX", "UYI", "VND", "VUV", "XAF", "XOF", "XPF"];
    }
    public function isNonFractionalCurrency()
    {
        return in_array($this->getCode(), $this->nonFractionalCurrencyCodes());
    }
    public function valueInCurrencyPrecision($amount)
    {
        if($this->isNonFractionalCurrency()) {
            $amount = round($amount);
        }
        return $amount;
    }
}

?>