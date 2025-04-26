<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

namespace WHMCS\Module\Gateway\paypal_ppcpv\Handler\Event;

// Decoded file for php version 72.
class PaymentCaptureDeclined extends PaymentCaptureDenied
{
    protected function message($invoiceId) : int
    {
        return sprintf("Payment Declined event for invoice %d has been received", $invoiceId);
    }
    protected function assertInvoice($invoiceId) : \WHMCS\Billing\Invoice
    {
        return $this->assertInvoiceWithMessage($invoiceId, "Payment Declined event received, but invoice #" . $invoiceId . " not found");
    }
    protected function getTransactionHistories($invoiceId, string $transactionIdentifier) : \Illuminate\Database\Eloquent\Collection
    {
        return $this->getTransactionHistoriesByStatus($invoiceId, $transactionIdentifier, "DECLINED");
    }
}

?>