<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Payment;

class TransactionAmount
{
    private $fee;
    private $amount;
    public function __construct(Contracts\MonetaryAmountInterface $amount, Contracts\MonetaryAmountInterface $fee)
    {
        $this->amount = $amount;
        $this->fee = $fee;
    }
    public function fee() : Contracts\MonetaryAmountInterface
    {
        return $this->fee;
    }
    public function amount() : Contracts\MonetaryAmountInterface
    {
        return $this->amount;
    }
}

?>