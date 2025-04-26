<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

namespace WHMCS\Module\Gateway\paypal_ppcpv\Handler\Event;

// Decoded file for php version 72.
class PaymentCaptureRefunded extends AbstractWebhookHandler
{
    use WebhookCaptureHandlerTrait;
    public function handle(\WHMCS\Module\Gateway\paypal_ppcpv\API\AbstractWebhookEvent $event, &$outcomes) : \WHMCS\Module\Gateway\paypal_ppcpv\API\AbstractWebhookEvent
    {
        $gateway = $event->initiatingModule();
        $this->assertUnique($gateway, $event->getTransactionId());
        $capturedTransactionId = $event->capturedTransactionIdentifier();
        $transaction = $this->assertKnownTransaction($gateway, $capturedTransactionId);
        $invoice = $this->assertInvoiceByTransaction($transaction);
        $this->assertPayloadCurrencyCode($event, $invoice);
        $refundMessage = $this->refundInvoice($event->getTransactionId(), $event->getAmount(), $transaction->id);
        if($refundMessage == "success") {
            $history = $this->newHistoryWithWebhookEventDetail($event, $gateway);
            $outcomes->transactionHistoryId = $history->id;
            return $this->success($invoice->id);
        }
        return $this->error($invoice->id, $refundMessage);
    }
    public function determineInitializingModule($invoiceId, string $transactionIdentifier) : int
    {
        return static::moduleNameByTransactionHistory($this->transactionHistoryByTransactionIdentifier($invoiceId, $transactionIdentifier));
    }
    protected function refundInvoice($refundIdentifier, $amount, int $transactionIdentifier) : int
    {
        return $this->refundingInvoice($refundIdentifier, $amount, $transactionIdentifier);
    }
    protected function refundingInvoice($refundIdentifier, $amount, int $transactionIdentifier = false, $reverse) : int
    {
        if(!function_exists("getCCVariables")) {
            require_once ROOTDIR . DIRECTORY_SEPARATOR . "includes" . DIRECTORY_SEPARATOR . "ccfunctions.php";
        }
        $result = refundInvoicePayment($transactionIdentifier, $amount, false, false, true, $refundIdentifier, $reverse);
        if(in_array($result, ["success", "manual"])) {
            return "success";
        }
        return $result;
    }
    protected function transactionHistoryByTransactionIdentifier($invoiceId, string $transactionIdentifier) : \WHMCS\Billing\Payment\Transaction\History
    {
        return \WHMCS\Billing\Payment\Transaction\History::where("invoice_id", $invoiceId)->where("transaction_id", $transactionIdentifier)->first();
    }
    protected function getTransaction($gateway, string $transactionId) : \WHMCS\Billing\Payment\Transaction
    {
        return \WHMCS\Billing\Payment\Transaction::where("gateway", $gateway)->where("transid", $transactionId)->first();
    }
    protected function assertUnique(string $gateway, string $transactionIdentifier)
    {
        $this->assertUniqueWithMessage($gateway, $transactionIdentifier, "Payment has already been refunded");
    }
    protected function assertUniqueWithMessage($gateway, string $transactionIdentifier, string $exceptionMessage) : void
    {
        if(!\WHMCS\Billing\Payment\Transaction::isUnique($gateway, $transactionIdentifier)) {
            throw new \Exception($exceptionMessage);
        }
    }
    protected function assertKnownTransaction($gateway, string $capturedTransactionId) : \WHMCS\Billing\Payment\Transaction
    {
        return $this->assertKnownTransactionWithMessage($gateway, $capturedTransactionId, "Failed to refund: unknown capture #" . $capturedTransactionId);
    }
    protected function assertKnownTransactionWithMessage($gateway, string $capturedTransactionId, string $exceptionMessage) : \WHMCS\Billing\Payment\Transaction
    {
        $transaction = $this->getTransaction($gateway, $capturedTransactionId);
        if(is_null($transaction)) {
            throw new \Exception($exceptionMessage);
        }
        return $transaction;
    }
    protected function assertInvoiceByTransaction(\WHMCS\Billing\Payment\Transaction $transaction) : \WHMCS\Billing\Invoice
    {
        return $this->assertInvoiceByTransactionWithMessage($transaction, "Payment Refund event received, but no invoice found");
    }
    protected function assertInvoiceByTransactionWithMessage(\WHMCS\Billing\Payment\Transaction $transaction, string $exceptionMessage) : \WHMCS\Billing\Invoice
    {
        $invoice = $transaction->invoice;
        if(is_null($invoice)) {
            throw new \Exception($exceptionMessage);
        }
        return $invoice;
    }
    protected function assertPayloadCurrencyCode(\WHMCS\Module\Gateway\paypal_ppcpv\API\AbstractWebhookEvent $event, \WHMCS\Billing\Invoice $invoice)
    {
        $this->assertCurrencyCode("Payment Refund", $event->getCurrentCode() ?? "", $invoice->client->currencyrel->code ?? "");
    }
    protected function success($invoiceId) : int
    {
        return $this->successMessage($invoiceId, "refunded");
    }
    protected function error($invoiceId, $error) : int
    {
        return $this->errorMessage($invoiceId, $error, "refund");
    }
    protected function successMessage($invoiceId, string $action) : int
    {
        return sprintf("Invoice %d has been %s", $invoiceId, $action);
    }
    protected function errorMessage($invoiceId, string $error, string $action) : int
    {
        return sprintf("Invoice %d %s failed: status %s", $invoiceId, $action, $error);
    }
}

?>