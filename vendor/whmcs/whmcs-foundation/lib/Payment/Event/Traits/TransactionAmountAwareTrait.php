<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Payment\Event\Traits;

trait TransactionAmountAwareTrait
{
    private $transactionAmount;
    public function transactionAmount() : \WHMCS\Payment\TransactionAmount
    {
        return $this->transactionAmount;
    }
    public function setTransactionAmount(\WHMCS\Payment\TransactionAmount $transactionAmount) : \self
    {
        $this->transactionAmount = $transactionAmount;
        return $this;
    }
    protected function hasTransactionAmount()
    {
        return !is_null($this->transactionAmount);
    }
    protected function assertTransactionAmount() : \self
    {
        if(!$this->hasTransactionAmount()) {
            throw \WHMCS\Payment\Exception\MissingRequirement::ofImplementor("transactionAmount", self::class);
        }
        return $this;
    }
}

?>