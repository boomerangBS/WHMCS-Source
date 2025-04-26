<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
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