<?php


namespace WHMCS\Module\Gateway\paypal_ppcpv\Handler\Event;
trait WebhookCaptureHandlerTrait
{
    protected function newHistory($gatewayIdentifier) : \WHMCS\Billing\Payment\Transaction\History
    {
        $history = new \WHMCS\Billing\Payment\Transaction\History();
        $history->gateway = $gatewayIdentifier;
        $history->description = "";
        return $history;
    }
    protected function newHistoryWithWebhookEvent(\WHMCS\Module\Gateway\paypal_ppcpv\API\AbstractWebhookEvent $event, string $gateway) : \WHMCS\Billing\Payment\Transaction\History
    {
        $history = $this->newHistory($gateway);
        $history->transactionId = $event->getTransactionId();
        $history->remoteStatus = $event->getResourceStatus();
        if($event->getSummary()) {
            $history->description = $event->getSummary();
        }
        $currencyId = \WHMCS\Module\Gateway\paypal_ppcpv\Util::safeLoadCurrencyId($event->getCurrentCode());
        if($currencyId) {
            $history->amount = $event->getAmount();
            $history->currencyId = $currencyId;
        }
        $history->additionalInformation = $gateway . "|" . $event->getRequest()->rawJson;
        return $history;
    }
    protected function newHistoryWithWebhookEventDetail(\WHMCS\Module\Gateway\paypal_ppcpv\API\AbstractWebhookEvent $event, string $gateway) : \WHMCS\Billing\Payment\Transaction\History
    {
        $history = $this->newHistoryWithWebhookEvent($event, $gateway);
        $history->invoiceId = $event->getInvoiceId();
        $history->completed = $event->isResourceStatusCompleted();
        $history->save();
        return $history;
    }
    protected function assertCurrencyCode(string $action, string $payloadCode, string $targetCode)
    {
        if($payloadCode == "" || $payloadCode != $targetCode) {
            throw new \Exception($action . ": Invalid currency");
        }
    }
    protected function assertInvoiceWithMessage($invoiceId = NULL, string $exceptionMessage) : \WHMCS\Billing\Invoice
    {
        $message = $exceptionMessage ?? "Invoice Not Found";
        if($invoiceId <= 0) {
            throw new \Exception($message);
        }
        $invoice = \WHMCS\Billing\Invoice::find($invoiceId);
        if(is_null($invoice)) {
            throw new \Exception($message);
        }
        return $invoice;
    }
}

?>