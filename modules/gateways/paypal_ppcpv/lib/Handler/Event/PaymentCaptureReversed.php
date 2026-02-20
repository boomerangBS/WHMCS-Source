<?php


namespace WHMCS\Module\Gateway\paypal_ppcpv\Handler\Event;
class PaymentCaptureReversed extends PaymentCaptureRefunded
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
        try {
            paymentReversed($event->getTransactionId(), $capturedTransactionId, $invoice->id, $gateway);
            $history = $this->newHistoryWithWebhookEventDetail($event, $gateway);
            $outcomes->transactionHistoryId = $history->id;
            return $this->success($invoice->id);
        } catch (\Exception $e) {
            return $this->error($invoice->id, $e->getMessage());
        }
    }
    protected function assertKnownTransaction($gateway, string $capturedTransactionId) : \WHMCS\Billing\Payment\Transaction
    {
        return $this->assertKnownTransactionWithMessage($gateway, $capturedTransactionId, "Failed to reverse: unknown capture #" . $capturedTransactionId);
    }
    protected function assertUnique($gateway, string $transactionIdentifier) : void
    {
        $this->assertUniqueWithMessage($gateway, $transactionIdentifier, "Payment has already been reversed");
    }
    protected function assertInvoiceByTransaction(\WHMCS\Billing\Payment\Transaction $transaction) : \WHMCS\Billing\Invoice
    {
        return $this->assertInvoiceByTransactionWithMessage($transaction, "Payment Reverse event received, but no invoice found");
    }
    protected function assertPayloadCurrencyCode(\WHMCS\Module\Gateway\paypal_ppcpv\API\AbstractWebhookEvent $event, \WHMCS\Billing\Invoice $invoice)
    {
        $this->assertCurrencyCode("Payment Reverse", $event->getCurrentCode() ?? "", $invoice->client->currencyrel->code ?? "");
    }
    protected function success($invoiceId) : int
    {
        return $this->successMessage($invoiceId, "reversed");
    }
    protected function error($invoiceId, $error) : int
    {
        return $this->errorMessage($invoiceId, $error, "reverse");
    }
}

?>