<?php

namespace WHMCS\Payment\Event\Traits;

trait PreTransactionAmountAwareTrait
{
    private $preTransactionBalance;
    public function preTransactionBalance() : \WHMCS\Payment\Contracts\MonetaryAmountInterface
    {
        return $this->preTransactionBalance;
    }
    public function setPreTransactionBalance(\WHMCS\Payment\Contracts\MonetaryAmountInterface $preTransactionBalance) : \self
    {
        $this->preTransactionBalance = $preTransactionBalance;
        return $this;
    }
    protected function hasPreTransactionBalance()
    {
        return !is_null($this->preTransactionBalance);
    }
    protected function assertPreTransactionBalance() : \self
    {
        if(!$this->hasPreTransactionBalance()) {
            throw \WHMCS\Payment\Exception\MissingRequirement::ofImplementor("preTransactionBalance", self::class);
        }
        return $this;
    }
}

?>