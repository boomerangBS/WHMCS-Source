<?php

namespace WHMCS\Payment\Event;

class InvoicePayment implements EventInterface
{
    use Traits\DateAwareTrait;
    use Traits\InvoiceAwareTrait;
    use Traits\PaymentGatewayAwareTrait;
    use Traits\PreTransactionAmountAwareTrait;
    use Traits\TransactionAmountAwareTrait;
    use Traits\TransactionAwareTrait;
    public function make() : EventInterface
    {
        return $this->assertInvoice()->assertDate()->assertPaymentGateway()->assertPreTransactionBalance()->assertTransactionAmount()->assertTransaction();
    }
}

?>