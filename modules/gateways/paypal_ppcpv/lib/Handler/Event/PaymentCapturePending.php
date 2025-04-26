<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

namespace WHMCS\Module\Gateway\paypal_ppcpv\Handler\Event;

// Decoded file for php version 72.
class PaymentCapturePending extends AbstractWebhookHandler
{
    use WebhookCaptureHandlerTrait;
    public function handle(\WHMCS\Module\Gateway\paypal_ppcpv\API\AbstractWebhookEvent $event, &$outcomes) : \WHMCS\Module\Gateway\paypal_ppcpv\API\AbstractWebhookEvent
    {
        $gateway = $event->initiatingModule();
        $invoice = $this->assertInvoice($event->getInvoiceId());
        $history = $this->transactionHistory($invoice->id, $event->getTransactionId(), $gateway);
        if(is_null($history)) {
            $history = $this->newHistoryWithWebhookEventDetail($event, $gateway);
        }
        $outcomes->transactionHistoryId = $history->id;
        return $this->message($invoice->id);
    }
    protected function message($invoiceId) : int
    {
        return sprintf("Payment Pending event for invoice %d has been received", $invoiceId);
    }
    protected function assertInvoice($invoiceId) : \WHMCS\Billing\Invoice
    {
        return $this->assertInvoiceWithMessage($invoiceId, "Payment Pending event received, but invoice #" . $invoiceId . " not found");
    }
    protected function transactionHistory($invoiceId, string $transactionIdentifier, string $gateway) : \WHMCS\Billing\Payment\Transaction\History
    {
        $histories = $this->getTransactionHistories($invoiceId, $transactionIdentifier);
        foreach ($histories as $history) {
            if($history->additionalInformation == "") {
            } else {
                $unpackedData = \WHMCS\Module\Gateway\paypal_ppcpv\Logger::historyUnpackAdditional($history->additionalInformation);
                if(is_object($unpackedData) && $gateway == $unpackedData->moduleName) {
                    return $history;
                }
            }
        }
        return NULL;
    }
    protected function getTransactionHistories($invoiceId, string $transactionIdentifier) : \Illuminate\Database\Eloquent\Collection
    {
        return $this->getTransactionHistoriesByStatus($invoiceId, $transactionIdentifier, "PENDING");
    }
    protected function getTransactionHistoriesByStatus($invoiceId, string $transactionIdentifier, string $status) : \Illuminate\Database\Eloquent\Collection
    {
        return \WHMCS\Billing\Payment\Transaction\History::where("invoice_id", $invoiceId)->where("transaction_id", $transactionIdentifier)->where("remote_status", $status)->get();
    }
}

?>