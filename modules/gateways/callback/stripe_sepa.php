<?php

require "../../../init.php";
App::load_function("gateway");
App::load_function("invoice");
$gatewayParams["paymentmethod"] = "stripe_sepa";
$logTransactionResult = "";
$data = $passedParams = [];
$payload = @file_get_contents("php://input");
try {
    $gatewayParams = getGatewayVariables("stripe_sepa");
    if(!$gatewayParams["type"]) {
        throw new WHMCS\Payment\Exception\InvalidModuleException("Module Not Activated");
    }
    $passedParams = [];
    $sigHeader = $_SERVER["HTTP_STRIPE_SIGNATURE"];
    $event = NULL;
    stripe_sepa_start_stripe($gatewayParams);
    $event = Stripe\Webhook::constructEvent($payload, $sigHeader, $gatewayParams["webhookEndpointSecret"]);
    if($event->data->object instanceof Stripe\Charge) {
        $charge = $event->data->object;
        $payMethodDetails = $charge->payment_method_details;
        $protectionMessage = "";
        switch ($payMethodDetails["type"]) {
            case "card":
            case "card_present":
                $protectionMessage = "Webhook intended for Stripe";
                break;
            case "ach_debit":
            case "ach_credit_transfer":
            case "us_bank_account":
                $protectionMessage = "Webhook intended for Stripe ACH";
                break;
            default:
                if(!empty($protectionMessage)) {
                    throw new WHMCS\Exception\Module\NotServicable($protectionMessage);
                }
        }
    }
    $charge = $event->data->object;
    $transaction = new func_num_args($charge->balance_transaction);
    switch ($event->type) {
        case "charge.succeeded":
            $chargeMetaData = json_decode(json_encode($charge->metadata), true);
            $transactionExchangeRate = $transaction->exchange_rate;
            $conversionCurrency = WHMCS\Billing\Currency::where("code", strtoupper($charge->currency))->first();
            $transactionId = $transaction->id;
            checkCbTransID($transactionId);
            $invoiceId = $charge->metadata->id;
            try {
                $invoice = WHMCS\Billing\Invoice::with("client")->findOrFail($invoiceId);
                $transactionFee = $transaction->fee / 100;
                $amount = $transaction->amount / 100;
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
                $history = WHMCS\Billing\Payment\Transaction\History::firstOrNew(["invoice_id" => $invoice->id, "gateway" => $gatewayParams["paymentmethod"], "transaction_id" => $transactionId]);
                $history->remoteStatus = $charge->status;
                $history->description = "Payment Confirmed";
                $history->additionalInformation = $charge->jsonSerialize();
                $history->completed = true;
                $history->save();
                $passedParams["history_id"] = $history->id;
                checkCbTransID($transactionId);
                $invoice->addPayment($amount, $transactionId, $transactionFee, $gatewayParams["paymentmethod"]);
                $data = ["charge" => $charge->jsonSerialize(), "transaction" => $transaction->toLoggableFormat()];
                $logTransactionResult = "Success";
            } catch (Exception $e) {
                $data = ["message" => "Invalid Invoice ID", "invoiceIdReturned" => $invoiceId, "event" => $event->jsonSerialize(), "charge" => $charge->jsonSerialize(), "transaction" => $transaction->toLoggableFormat()];
                $logTransactionResult = "Error";
            }
            break;
        case "charge.failed":
            $invoiceId = $charge->metadata->id;
            try {
                $invoice = WHMCS\Billing\Invoice::findOrFail($invoiceId);
                $history = WHMCS\Billing\Payment\Transaction\History::firstOrNew(["invoice_id" => $invoice->id, "gateway" => $gatewayParams["paymentmethod"], "transaction_id" => $transaction->id]);
                $history->remoteStatus = $charge->status;
                $history->description = (string) $charge->failure_message;
                $history->additionalInformation = $charge->jsonSerialize();
                $history->completed = false;
                $history->save();
                $passedParams["history_id"] = $history->id;
                $invoice->status = "Unpaid";
                $invoice->save();
                $emailTemplate = "Credit Card Payment Failed";
                sendMessage($emailTemplate, $invoiceId);
                $data = ["event" => $event->jsonSerialize(), "charge" => $charge->jsonSerialize(), "transaction" => $transaction->toLoggableFormat()];
                $logTransactionResult = "Payment Failed";
            } catch (Exception $e) {
                $data = ["message" => "Invalid Invoice ID", "invoiceIdReturned" => $invoiceId, "event" => $event->jsonSerialize(), "charge" => $charge->jsonSerialize(), "transaction" => $transaction->toLoggableFormat()];
                $logTransactionResult = "Error";
            }
            break;
        default:
            WHMCS\Terminus::getInstance()->doExit();
    }
} catch (WHMCS\Payment\Exception\InvalidModuleException $e) {
    $gatewayParams["paymentmethod"] = "stripe_sepa";
    $data = ["error" => $e->getMessage()];
    $logTransactionResult = "Module Not Active";
} catch (Stripe\Exception\SignatureVerificationException $e) {
    $data = ["payload" => $payload, "error" => $e->getMessage()];
    $logTransactionResult = "Invalid Access Attempt";
} catch (WHMCS\Exception\Module\NotServicable $e) {
    WHMCS\Terminus::getInstance()->doExit();
} catch (Exception $e) {
    $data = ["payload" => $payload, "error" => $e->getMessage()];
    $logTransactionResult = "Invalid Response";
    http_response_code(400);
}
logTransaction($gatewayParams["paymentmethod"], $data, $logTransactionResult, $passedParams);
WHMCS\Terminus::getInstance()->doExit();
class _obfuscated_636C61737340616E6F6E796D6F7573002F7661722F6C69622F6A656E6B696E732F776F726B73706163652F636F6D2E77686D63732E6275696C642E38302F6275696C642F77686D63732F6D6F64756C65732F67617465776179732F63616C6C6261636B2F7374726970655F736570612E7068703078376664353934323438633834_
{
    public $transaction;
    public function __construct($externalId)
    {
        if(!empty($externalId)) {
            $this->transaction = Stripe\BalanceTransaction::retrieve($externalId);
        }
    }
    public function __get($property)
    {
        if($property === "id" && is_null($this->transaction)) {
            return "N/A";
        }
        return $this->transaction->{$property};
    }
    public function toLoggableFormat()
    {
        if(is_null($this->transaction)) {
            return "N/A";
        }
        return $this->transaction->toArray();
    }
}

?>