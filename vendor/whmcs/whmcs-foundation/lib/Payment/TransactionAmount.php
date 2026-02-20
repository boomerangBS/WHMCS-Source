<?php

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