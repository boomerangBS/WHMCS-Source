<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
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