<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Payment\Contracts;

interface PayMethodTypeInterface
{
    const TYPE_BANK_ACCOUNT = "BankAccount";
    const TYPE_REMOTE_BANK_ACCOUNT = "RemoteBankAccount";
    const TYPE_CREDITCARD_LOCAL = "CreditCard";
    const TYPE_CREDITCARD_REMOTE_MANAGED = "RemoteCreditCard";
    const TYPE_CREDITCARD_REMOTE_UNMANAGED = "PayToken";
    public function getType($instance);
    public function getTypeDescription($instance);
    public function isManageable();
    public function isCreditCard();
    public function isLocalCreditCard();
    public function isRemoteCreditCard();
    public function isBankAccount();
    public function isRemoteBankAccount();
}

?>