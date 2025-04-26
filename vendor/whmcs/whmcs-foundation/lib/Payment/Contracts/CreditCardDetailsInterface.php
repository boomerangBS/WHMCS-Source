<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Payment\Contracts;

interface CreditCardDetailsInterface
{
    public function getCardNumber();
    public function setCardNumber($value);
    public function getCardCvv();
    public function setCardCvv($value);
    public function getLastFour();
    public function setLastFour($value);
    public function getMaskedCardNumber();
    public function getExpiryDate();
    public function setExpiryDate(\WHMCS\Carbon $value);
    public function getCardType();
    public function setCardType($value);
    public function getStartDate();
    public function setStartDate(\WHMCS\Carbon $value);
    public function getIssueNumber();
    public function setIssueNumber($value);
}

?>