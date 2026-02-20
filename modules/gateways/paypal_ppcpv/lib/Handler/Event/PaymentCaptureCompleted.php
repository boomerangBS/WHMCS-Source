<?php


namespace WHMCS\Module\Gateway\paypal_ppcpv\Handler\Event;
class PaymentCaptureCompleted extends AbstractWebhookHandler
{
    use WebhookCaptureHandlerTrait;
    public function handle(\WHMCS\Module\Gateway\paypal_ppcpv\API\AbstractWebhookEvent $event, &$outcomes) : \WHMCS\Module\Gateway\paypal_ppcpv\API\AbstractWebhookEvent
    {
        $actionName = "Payment Complete";
        $payloadInvoiceId = $event->getInvoiceId();
        if($payloadInvoiceId <= 0) {
            throw new \Exception($actionName . ": Invoice ID is empty.");
        }
        $gateway = $event->initiatingModule();
        $unpaidInvoice = \WHMCS\Billing\Invoice::unpaidOrPaymentPending()->where("id", $payloadInvoiceId)->first();
        if(!is_null($unpaidInvoice)) {
            $this->assertCurrencyCode($actionName, $event->getCurrentCode() ?? "", $unpaidInvoice->getCurrency()["code"] ?? "");
            try {
                $unpaidInvoice->addPaymentIfNotExists($event->getAmount(), $event->getTransactionId(), $event->getSellerFee(), $gateway);
            } catch (\WHMCS\Exception\Module\NotServicable $e) {
                throw new \Exception($actionName . ": Transaction ID already exists");
            }
            $history = $this->newHistoryWithWebhookEvent($event, $gateway);
            $history->invoiceId = $unpaidInvoice->id;
            $history->completed = 1;
            $history->save();
            $outcomes->transactionHistoryId = $history->id;
            return $actionName;
        }
        $invoice = \WHMCS\Billing\Invoice::find($payloadInvoiceId);
        if(is_null($invoice)) {
            throw new \Exception($actionName . ": No invoice found");
        }
        if(is_null($invoice->client)) {
            throw new \Exception($actionName . ": Missing invoice client");
        }
        $this->assertCurrencyCode($actionName, $event->getCurrentCode() ?? "", $invoice->client->currencyrel->code ?? "");
        \WHMCS\Billing\Payment\Transaction::assertUnique($gateway, $event->getTransactionId());
        $transaction = $this->newTransaction($event->getTransactionId(), $invoice->client, $gateway);
        $transaction->description = "PayPal " . $actionName;
        $transaction->amountIn = $event->getAmount();
        $transaction->fees = $event->getSellerFee();
        $transaction->save();
        $invoice->client->addCredit("PayPal Payment Transaction ID " . $event->getTransactionId(), $event->getAmount());
        $history = $this->newHistoryWithWebhookEvent($event, $gateway);
        $history->invoiceId = $invoice->id;
        $history->completed = 1;
        $history->description = "PayPal Additional " . $actionName;
        $history->save();
        $outcomes->transactionHistoryId = $history->id;
        return $actionName;
    }
    protected function newTransaction($transactionId, $client, string $gateway) : \WHMCS\Billing\Payment\Transaction
    {
        $transaction = new \WHMCS\Billing\Payment\Transaction();
        $transaction->transactionId = $transactionId;
        $transaction->gateway = $gateway;
        $transaction->clientId = $client->id;
        $transaction->currency = $client->currencyrel->id;
        $transaction->exchangeRate = $client->currencyrel->rate;
        $transaction->date = \WHMCS\Carbon::now();
        return $transaction;
    }
}

?>