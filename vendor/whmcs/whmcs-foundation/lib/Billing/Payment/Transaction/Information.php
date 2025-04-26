<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Billing\Payment\Transaction;

class Information implements \Illuminate\Contracts\Support\Arrayable
{
    protected $transactionId = "";
    protected $relatedTransactions = [];
    protected $type;
    protected $amount;
    protected $currency;
    protected $fee;
    protected $feeCurrency;
    protected $merchantAmount;
    protected $merchantCurrency;
    protected $description;
    protected $status;
    protected $exchangeRate;
    protected $created;
    protected $availableOn;
    protected $additionalData = [];
    public function setTransactionId($transactionId) : \self
    {
        $this->transactionId = $transactionId;
        return $this;
    }
    public function setAmount($amount = NULL, $currency) : \self
    {
        $this->amount = $amount;
        if(!is_null($currency)) {
            $this->setCurrency($currency);
        }
        return $this;
    }
    public function setFee($fee = NULL, $currency) : \self
    {
        $this->fee = $fee;
        if(!is_null($currency)) {
            $this->setFeeCurrency($currency);
        }
        return $this;
    }
    public function setMerchantAmount($amount = NULL, $currency) : \self
    {
        $this->merchantAmount = $amount;
        if(!is_null($currency)) {
            $this->setMerchantCurrency($currency);
        }
        return $this;
    }
    public function setType($type) : \self
    {
        $this->type = $type;
        return $this;
    }
    public function setCurrency(\WHMCS\Billing\CurrencyInterface $currency) : \self
    {
        $this->currency = $currency;
        return $this;
    }
    public function setFeeCurrency(\WHMCS\Billing\CurrencyInterface $currency) : \self
    {
        $this->feeCurrency = $currency;
        return $this;
    }
    public function setMerchantCurrency(\WHMCS\Billing\CurrencyInterface $currency) : \self
    {
        $this->merchantCurrency = $currency;
        return $this;
    }
    public function setDescription($description) : \self
    {
        $this->description = $description;
        return $this;
    }
    public function setStatus($status) : \self
    {
        $this->status = $status;
        return $this;
    }
    public function setExchangeRate($exchangeRate) : Information
    {
        $this->exchangeRate = $exchangeRate;
        return $this;
    }
    public function setCreated(\WHMCS\Carbon $created) : \self
    {
        $this->created = $created;
        return $this;
    }
    public function setAvailableOn(\WHMCS\Carbon $availableOn) : \self
    {
        $this->availableOn = $availableOn;
        return $this;
    }
    public function setAdditionalData($additionalData) : \self
    {
        $this->additionalData = $additionalData;
        return $this;
    }
    public function setAdditionalDatum($key, $value) : \self
    {
        $this->additionalData[$key] = $value;
        return $this;
    }
    public function toArray() : array
    {
        $return = ["transactionId" => $this->getTransactionId(), "type" => $this->getType(), "status" => $this->getStatus(), "description" => $this->getDescription(), "exchangeRate" => $this->getExchangeRate(), "created" => $this->getCreated(), "availableOn" => $this->getAvailableOn(), "relatedTransaction" => $this->getRelatedTransactions()];
        $currencyRenderer = function ($amount, \WHMCS\Billing\CurrencyInterface $c) {
            $numericAmount = formatCurrency($amount)->toNumeric();
            if(is_null($c)) {
                return $numericAmount;
            }
            return sprintf("%s %s", $numericAmount, $c->getCode());
        };
        if(!is_null($this->getAmount())) {
            $return["amount"] = $currencyRenderer($this->getAmount(), $this->getCurrency());
        }
        if(!is_null($this->getFee())) {
            $return["fee"] = $currencyRenderer($this->getFee(), $this->getFeeCurrency());
        }
        if(!is_null($this->getMerchantAmount())) {
            $return["merchant_amount"] = $currencyRenderer($this->getMerchantAmount(), $this->getMerchantAmountCurrency());
        }
        return array_merge($return, $this->getAdditionalData());
    }
    protected function getTransactionId()
    {
        return $this->transactionId;
    }
    protected function getAmount()
    {
        return $this->amount;
    }
    protected function getType()
    {
        return $this->type;
    }
    protected function getCurrency() : \WHMCS\Billing\CurrencyInterface
    {
        return $this->currency;
    }
    protected function getFeeCurrency() : \WHMCS\Billing\CurrencyInterface
    {
        return $this->feeCurrency;
    }
    protected function getMerchantAmountCurrency() : \WHMCS\Billing\CurrencyInterface
    {
        return $this->merchantCurrency;
    }
    protected function getDescription()
    {
        return $this->description;
    }
    protected function getStatus()
    {
        return $this->status;
    }
    protected function getExchangeRate()
    {
        return $this->exchangeRate;
    }
    protected function getCreated()
    {
        return $this->created ? $this->created->toAdminDateTimeFormat() : NULL;
    }
    protected function getAvailableOn()
    {
        return $this->availableOn ? $this->availableOn->toAdminDateTimeFormat() : NULL;
    }
    protected function getFee()
    {
        return $this->fee;
    }
    protected function getMerchantAmount()
    {
        return $this->merchantAmount;
    }
    protected function getAdditionalData() : array
    {
        return $this->additionalData;
    }
    public function addRelatedTransaction($identifier = "", string $type) : \self
    {
        $o = new func_num_args();
        $o->identifier = $identifier;
        $o->type = $type;
        $this->relatedTransactions[] = $o;
        return $this;
    }
    public function getRelatedTransactions() : array
    {
        return $this->relatedTransactions;
    }
}
class _obfuscated_5C636C61737340616E6F6E796D6F7573002F7661722F6C69622F6A656E6B696E732F776F726B73706163652F636F6D2E77686D63732E6275696C642E38302F6275696C642F77686D63732F76656E646F722F77686D63732F77686D63732D666F756E646174696F6E2F6C69622F42696C6C696E672F5061796D656E742F5472616E73616374696F6E2F496E666F726D6174696F6E2E7068703078376664353934323461393362_
{
    public $identifier = "";
    public $type = "";
    public function __toString()
    {
        $out = $this->identifier;
        if($this->type != "") {
            $out .= " (" . $this->type . ")";
        }
        return $out;
    }
}

?>