<?php


namespace WHMCS\Module\Gateway\paypal_ppcpv\Handler\Event;
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