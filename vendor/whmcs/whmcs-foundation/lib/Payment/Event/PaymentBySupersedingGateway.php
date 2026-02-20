<?php

namespace WHMCS\Payment\Event;

class PaymentBySupersedingGateway implements EventInterface
{
    use Traits\InvoiceAwareTrait;
    use Traits\PaymentGatewayAwareTrait;
    public function make() : EventInterface
    {
        return $this->assertInvoice()->assertPaymentGateway();
    }
}

?>