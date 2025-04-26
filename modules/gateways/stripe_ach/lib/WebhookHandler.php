<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Module\Gateway\StripeAch;

class WebhookHandler
{
    protected $data = [];
    protected $event;
    protected $gatewayParams = [];
    protected $logType = "";
    protected $passedParams = [];
    protected function __construct()
    {
    }
    protected function setData($data) : \self
    {
        $this->data = $data;
        return $this;
    }
    protected function setLogType($logType) : \self
    {
        $this->logType = $logType;
        return $this;
    }
    protected function setPassedParams($params) : \self
    {
        $this->passedParams = $params;
        return $this;
    }
    protected function setPassedParam($key, $value) : \self
    {
        $this->passedParams[$key] = $value;
        return $this;
    }
    public static function factory() : \self
    {
        $payload = @file_get_contents("php://input");
        $gatewayParams = getGatewayVariables("stripe_ach");
        if(!$gatewayParams["type"]) {
            throw new \WHMCS\Payment\Exception\InvalidModuleException("Module Not Activated");
        }
        $sigHeader = $_SERVER["HTTP_STRIPE_SIGNATURE"];
        stripe_ach_start_stripe($gatewayParams);
        $event = \Stripe\Webhook::constructEvent($payload, $sigHeader, $gatewayParams["webhookEndpointSecret"]);
        $self = new self();
        $self->event = $event;
        $self->gatewayParams = $gatewayParams;
        $self->passedParams = [];
        return $self;
    }
    public function chargeFailed() : \self
    {
        $event = $this->event;
        $gatewayParams = $this->gatewayParams;
        $charge = $event->data->object;
        $invoiceId = $charge->metadata->id;
        $transaction = \Stripe\BalanceTransaction::retrieve($charge->balance_transaction);
        try {
            $invoice = \WHMCS\Billing\Invoice::findOrFail($invoiceId);
            $history = \WHMCS\Billing\Payment\Transaction\History::firstOrNew(["invoice_id" => $invoice->id, "gateway" => $gatewayParams["paymentmethod"], "transaction_id" => $transaction->id]);
            $history->remoteStatus = $charge->status;
            $history->description = $charge->failure_message;
            $history->additionalInformation = $charge->jsonSerialize();
            $history->completed = false;
            $history->save();
            $this->setPassedParam("history_id", $history->id);
            $invoice->status = \WHMCS\Billing\Invoice::STATUS_UNPAID;
            $invoice->save();
            $emailTemplate = "Direct Debit Payment Failed";
            sendMessage($emailTemplate, $invoiceId);
            $data = ["event" => $event->jsonSerialize(), "charge" => $charge->jsonSerialize(), "transaction" => $transaction->jsonSerialize()];
            $logTransactionResult = "Payment Failed";
        } catch (\Throwable $e) {
            $data = ["message" => "Invalid Invoice ID", "invoiceIdReturned" => $invoiceId, "event" => $event->jsonSerialize(), "charge" => $charge->jsonSerialize(), "transaction" => $transaction->jsonSerialize()];
            $logTransactionResult = "Error";
        }
        return $this->setData($data)->setLogType($logTransactionResult);
    }
    public function chargeSucceeded() : \self
    {
        $event = $this->event;
        $gatewayParams = $this->gatewayParams;
        $charge = $event->data->object;
        $transaction = \Stripe\BalanceTransaction::retrieve($charge->balance_transaction);
        $transactionExchangeRate = $transaction->exchange_rate;
        $conversionCurrency = \WHMCS\Billing\Currency::where("code", strtoupper($charge->currency))->first();
        $transactionId = $transaction->id;
        checkCbTransID($transactionId);
        $invoiceId = $charge->metadata->id;
        try {
            $invoice = \WHMCS\Billing\Invoice::with("client")->findOrFail($invoiceId);
            $transactionFee = \WHMCS\Module\Gateway\Stripe\ApiPayload::formatAmountInbound($transaction->fee, $charge->currency);
            $amount = \WHMCS\Module\Gateway\Stripe\ApiPayload::formatAmountInbound($transaction->amount, $charge->currency);
            if($transactionExchangeRate) {
                $transactionFee /= $transactionExchangeRate;
                $amount /= $transactionExchangeRate;
                $convertCurrency = $gatewayParams["convertto"];
                if(!$convertCurrency) {
                    $convertCurrency = $invoice->client->currencyId;
                }
                if($convertCurrency && $conversionCurrency) {
                    $transactionFee = convertCurrency($transactionFee, $conversionCurrency->id, $convertCurrency);
                    $amount = convertCurrency($amount, $conversionCurrency->id, $convertCurrency);
                }
            }
            $history = \WHMCS\Billing\Payment\Transaction\History::firstOrNew(["invoice_id" => $invoice->id, "gateway" => $gatewayParams["paymentmethod"], "transaction_id" => $transactionId]);
            $history->remoteStatus = $charge->status;
            $history->description = "Payment Confirmed";
            $history->additionalInformation = $charge->jsonSerialize();
            $history->completed = true;
            $history->save();
            $this->setPassedParam("history_id", $history->id);
            $invoice->addPayment($amount, $transactionId, $transactionFee, $gatewayParams["paymentmethod"]);
            $data = ["charge" => $charge->jsonSerialize(), "transaction" => $transaction->jsonSerialize()];
            $logTransactionResult = "Success";
        } catch (\Throwable $e) {
            $data = ["message" => "Invalid Invoice ID", "invoiceIdReturned" => $invoiceId, "event" => $event->jsonSerialize(), "charge" => $charge->jsonSerialize(), "transaction" => $transaction->jsonSerialize()];
            $logTransactionResult = "Error";
        }
        return $this->setData($data)->setLogType($logTransactionResult);
    }
    public function getData() : array
    {
        return $this->data;
    }
    public function getLogType()
    {
        return $this->logType;
    }
    public function getPassedParams() : array
    {
        return $this->passedParams;
    }
    public function handleEvent() : \self
    {
        switch ($this->event->type) {
            case "charge.succeeded":
                return $this->chargeSucceeded();
                break;
            case "charge.failed":
                return $this->chargeFailed();
                break;
            default:
                throw new \WHMCS\Exception\Module\NotServicable("Unsupported Event");
        }
    }
    public function validateEventForModule() : \self
    {
        $eventObject = $this->event->data->object;
        if($eventObject instanceof \Stripe\Charge) {
            $payMethodDetails = $eventObject->payment_method_details;
            switch ($payMethodDetails["type"]) {
                case "card":
                case "card_present":
                    $protectionMessage = "Webhook intended for Stripe";
                    break;
                case "sepa_debit":
                    $protectionMessage = "Webhook intended for Stripe SEPA";
                    throw new \WHMCS\Exception\Module\NotServicable($protectionMessage);
                    break;
                default:
                    return $this;
            }
        } else {
            return $this;
        }
    }
}

?>