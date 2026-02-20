<?php


namespace WHMCS\Module\Gateway\Stripe;
class _obfuscated_636C61737340616E6F6E796D6F7573002F7661722F6C69622F6A656E6B696E732F776F726B73706163652F636F6D2E77686D63732E6275696C642E38302F6275696C642F77686D63732F6D6F64756C65732F67617465776179732F7374726970652F6C69622F4170695061796C6F61642E7068703078376664353934323439663266_
{
    public $source;
    public $accessor;
    public function __construct($source)
    {
        $this->source = $source;
        if($this->source instanceof WHMCS\Application) {
            $this->accessor = function ($property) {
                return $this->source->getFromRequest($property);
            };
        } elseif(is_array($this->source) || $this->source instanceof WHMCS\Model\AbstractModel) {
            $this->accessor = function ($property) {
                if(isset($this->source[$property])) {
                    return $this->source[$property];
                }
                return NULL;
            };
        } elseif(is_object($this->source)) {
            $this->accessor = function ($property) {
                if(property_exists($this->source, $property)) {
                    return $this->source->{$property};
                }
                return NULL;
            };
        }
    }
    public function __get(string $property)
    {
        if(method_exists($this, $property)) {
            return $this->{$property}();
        }
        return call_user_func($this->accessor, $property);
    }
    public function fullName()
    {
        if($this->source instanceof WHMCS\User\Client) {
            $name = $this->source->fullName;
        } else {
            $name = trim(sprintf("%s %s", $this->firstname, $this->lastname));
        }
        return $name;
    }
}
class ApiPayload
{
    public static function isNoDecimalCurrency($currencyCode)
    {
        return in_array(strtoupper($currencyCode), Constant::STRIPE_CURRENCIES_NO_DECIMALS);
    }
    public static function formatValue($value)
    {
        return $value !== "" ? $value : NULL;
    }
    public static function formatAmountOutbound($amount, $currencyCode)
    {
        $amount = str_replace([",", "."], "", $amount);
        if(self::isNoDecimalCurrency($currencyCode)) {
            $amount = round($amount / 100);
        }
        return (string) $amount;
    }
    public static function formatAmountInbound($amount, $currencyCode)
    {
        if(!self::isNoDecimalCurrency($currencyCode)) {
            $amount /= 100;
        }
        return (double) $amount;
    }
    public static function customer($source = NULL, $clientId) : array
    {
        $identity = static::identity($source);
        return array_merge($identity, ["description" => "Customer for " . $identity["name"] . " (" . $identity["email"] . ")", "address" => static::address($source), "metadata" => static::metaData($source, $clientId)]);
    }
    public static function paymentContact($source = NULL, $clientId) : array
    {
        return ["billing_details" => array_merge(static::identity($source), ["address" => static::address($source)]), "metadata" => static::metaData($source, $clientId)];
    }
    public static function identity($source) : array
    {
        $getter = static::getterFacade($source);
        return ["name" => $getter->fullName, "email" => $getter->email];
    }
    public static function address($source) : array
    {
        $getter = static::getterFacade($source);
        return ["line1" => static::formatValue($getter->address1), "line2" => static::formatValue($getter->address2), "city" => static::formatValue($getter->city), "state" => static::formatValue($getter->state), "country" => static::formatValue($getter->country), "postal_code" => static::formatValue($getter->postcode)];
    }
    public static function metaData($source = NULL, $clientId) : array
    {
        $identity = static::identity($source);
        $data = ["fullName" => $identity["name"], "email" => $identity["email"]];
        if($clientId !== NULL) {
            $data["clientId"] = $clientId;
        }
        return $data;
    }
    public static function getterFacade($source)
    {
        return new func_num_args($source);
    }
    public static function hasTransactionFee(\Stripe\BalanceTransaction $transaction) : \Stripe\BalanceTransaction
    {
        return 0 <= $transaction->fee;
    }
    public static function transactionFeeCurrency(\Stripe\BalanceTransaction $transaction) : \WHMCS\Billing\CurrencyInterface
    {
        $exchangeRate = (double) $transaction->exchange_rate;
        if($exchangeRate == 0) {
            $exchangeRate = 0;
        }
        $currency = new \WHMCS\Billing\CurrencyData();
        $currency->setRate($exchangeRate);
        $currency->setCode(strtoupper($transaction->currency));
        if(!self::hasTransactionFee($transaction) || !is_array($transaction->fee_details)) {
            return $currency;
        }
        $feeCurrencyCode = strtoupper($transaction->fee_details[0]->currency);
        $localCurrency = \WHMCS\Billing\Currency::where(["code" => $feeCurrencyCode])->first();
        if(!is_null($localCurrency)) {
            $currency = $localCurrency;
        } else {
            $currency->setRate($exchangeRate);
            $currency->setCode($feeCurrencyCode);
        }
        return $currency;
    }
    public static function transactionFee(\Stripe\BalanceTransaction $transactionData, $currencyToConvertTo) : \Stripe\BalanceTransaction
    {
        $feeCurrency = self::transactionFeeCurrency($transactionData);
        if(is_null($currencyToConvertTo)) {
            $currencyToConvertTo = new \WHMCS\Billing\CurrencyData();
            $currencyToConvertTo->setRate(0);
        }
        $transactionFee = self::formatAmountInbound($transactionData->fee, $feeCurrency->getCode());
        return \WHMCS\Billing\Currency::convertBetween($feeCurrency, $transactionFee, $currencyToConvertTo);
    }
}

?>