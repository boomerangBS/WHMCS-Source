<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

namespace WHMCS\Module\Gateway\paypal_ppcpv\Handler\Event;

// Decoded file for php version 72.
class PaymentCaptureDenied extends PaymentCapturePending
{
    use WebhookCaptureHandlerTrait;
    public function handle(\WHMCS\Module\Gateway\paypal_ppcpv\API\AbstractWebhookEvent $event, &$outcomes) : \WHMCS\Module\Gateway\paypal_ppcpv\API\AbstractWebhookEvent
    {
        $gateway = $event->initiatingModule();
        $invoice = $this->assertInvoice($event->getInvoiceId());
        $history = $this->transactionHistory($invoice->id, $event->getTransactionId(), $gateway);
        if(is_null($history)) {
            $history = $this->newHistoryWithWebhookEventDetail($event, $gateway);
            sendMessage("Credit Card Payment Failed", $invoice->id);
        }
        $outcomes->transactionHistoryId = $history->id;
        return $this->message($invoice->id);
    }
    protected function message($invoiceId) : int
    {
        return sprintf("Payment Denied event for invoice %d has been received", $invoiceId);
    }
    protected function assertInvoice($invoiceId) : \WHMCS\Billing\Invoice
    {
        return $this->assertInvoiceWithMessage($invoiceId, "Payment Denied event received, but invoice #" . $invoiceId . " not found");
    }
    protected function getTransactionHistories($invoiceId, string $transactionIdentifier) : \Illuminate\Database\Eloquent\Collection
    {
        return $this->getTransactionHistoriesByStatus($invoiceId, $transactionIdentifier, "REFUNDED");
    }
}

?>