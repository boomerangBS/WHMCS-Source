<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Payment\Contracts;

interface BankAccountDetailsInterface
{
    public function getRoutingNumber();
    public function setRoutingNumber($value);
    public function getAccountNumber();
    public function getMaskedAccountNumber();
    public function setAccountNumber($value);
    public function getBankName();
    public function setBankName($value);
    public function getAccountType();
    public function setAccountType($value);
    public function getAccountHolderName();
    public function setAccountHolderName($value);
}

?>