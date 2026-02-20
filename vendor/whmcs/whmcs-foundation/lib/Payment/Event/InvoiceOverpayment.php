<?php

namespace WHMCS\Payment\Event;

class InvoiceOverpayment implements EventInterface
{
    use Traits\DateAwareTrait;
    use Traits\InvoiceAwareTrait;
    use Traits\PreTransactionAmountAwareTrait;
    use Traits\TransactionAmountAwareTrait;
    public function make() : EventInterface
    {
        return $this->assertInvoice()->assertDate()->assertPreTransactionBalance()->assertTransactionAmount();
    }
}

?>