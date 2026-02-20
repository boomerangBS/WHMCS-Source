<?php

namespace WHMCS\Payment\Observer;

class AssignPaymentGatewayToInvoice implements ObserverInterface
{
    public function observe(\WHMCS\Payment\Event\PaymentBySupersedingGateway $event) : void
    {
        $event->invoice()->setPaymentMethod($event->paymentGateway()->systemIdentifier())->save();
    }
}

?>