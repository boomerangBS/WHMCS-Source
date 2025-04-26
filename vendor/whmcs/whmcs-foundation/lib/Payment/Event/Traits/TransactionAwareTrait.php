<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Payment\Event\Traits;

trait TransactionAwareTrait
{
    private $transaction;
    public function setTransaction(\WHMCS\Billing\Payment\Transaction $transaction) : \self
    {
        $this->transaction = $transaction;
        return $this;
    }
    public function transaction() : \WHMCS\Billing\Payment\Transaction
    {
        return $this->transaction;
    }
    public function hasTransaction()
    {
        return !is_null($this->transaction);
    }
    public function assertTransaction() : \self
    {
        if(!$this->hasTransaction()) {
            throw \WHMCS\Payment\Exception\MissingRequirement::ofImplementor("transaction", self::class);
        }
        return $this;
    }
}

?>