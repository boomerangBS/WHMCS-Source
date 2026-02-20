<?php

namespace WHMCS\Module\Gateway\paypal_ppcpv\Handler;

class Capture extends AbstractHandler
{
    public function handle(\WHMCS\Billing\Invoice $invoice = NULL, $paymentSource) : \WHMCS\Module\Gateway\paypal_ppcpv\ModuleFunctionResult\CaptureResult
    {
        $api = $this->api();
        $orderRequest = $this->decorateOrderRequest(new \WHMCS\Module\Gateway\paypal_ppcpv\API\CreateOrderRequest($api), $invoice);
        if(!is_null($paymentSource)) {
            $orderRequest->setPaymentSource($paymentSource);
        }
        $orderResponse = $api->send($orderRequest);
        if(!$orderResponse instanceof \WHMCS\Module\Gateway\paypal_ppcpv\API\CreateOrderResponse) {
            return $this->errorReturn($orderResponse->__toString());
        }
        return $this->handleResponse($orderResponse, $invoice);
    }
    private function handleResponse(\WHMCS\Module\Gateway\paypal_ppcpv\API\CreateOrderResponse $orderResponse, $invoice) : \WHMCS\Module\Gateway\paypal_ppcpv\ModuleFunctionResult\CaptureResult
    {
        $result = new \WHMCS\Module\Gateway\paypal_ppcpv\ModuleFunctionResult\CaptureResult();
        $result->fromApiResponse($orderResponse);
        if($result->isError()) {
            $reason = $result->get()["reason"];
            $this->log->gatewayOrder($orderResponse, "Failed");
            if($reason == "statusIncomplete") {
                $result->setRawData("Order Status Incomplete");
            } elseif($reason == "captureStatusIncomplete") {
                $result->setRawData("Capture Status Incomplete");
            }
            return $result;
        }
        if($orderResponse->isCapturePending()) {
            $invoice->setStatusPending()->save();
            $this->log->historyCapture($orderResponse, $invoice);
        } elseif($orderResponse->isCaptureComplete()) {
            $captureData = $orderResponse->captureData();
            if($captureData->amount->currency_code != $invoice->getCurrency()["code"]) {
                return $result->setNotSuccessful()->setStatus("error")->setReason("currencyMismatch")->setRawData("Currency Mismatch");
            }
            $result->setRawData(\WHMCS\Module\Gateway\paypal_ppcpv\Util::decodeJSON($orderResponse->packOrderResponse()))->setTransactionId($captureData->id)->setAmount($captureData->amount->value ?? 0)->setFee($captureData->seller_receivable_breakdown->paypal_fee->value ?? 0)->setTransactionHistoryId($this->log->history($orderResponse, $invoice)->id);
        } else {
            $this->log->historyCapture($orderResponse, $invoice);
            return $this->errorReturn("Capture Status Incomplete");
        }
        return $result;
    }
    protected function decorateOrderRequest(\WHMCS\Module\Gateway\paypal_ppcpv\API\CreateOrderRequest $orderRequest, $invoiceModel) : \WHMCS\Module\Gateway\paypal_ppcpv\API\CreateOrderRequest
    {
        $currency = $invoiceModel->getCurrency();
        $invoiceDescription = PaymentHandler::makeOrderDescription(\WHMCS\Config\Setting::getValue("CompanyName"), $invoiceModel);
        return $orderRequest->setAsCapture()->setPurchaseUnit($invoiceDescription, $invoiceModel->id, (string) \WHMCS\View\Formatter\Price::adjustDecimals($invoiceModel->total, $currency["code"]), $currency["code"]);
    }
    public function loadInvoice($invoiceId) : \WHMCS\Billing\Invoice
    {
        return \WHMCS\Billing\Invoice::find($invoiceId);
    }
    public function errorReturn($rawData) : \WHMCS\Module\Gateway\paypal_ppcpv\ModuleFunctionResult\CaptureResult
    {
        return (new \WHMCS\Module\Gateway\paypal_ppcpv\ModuleFunctionResult\CaptureResult())->setNotSuccessful()->setStatus("error")->setRawData($rawData);
    }
    public function errorInvalidInvoice($invoiceId) : \WHMCS\Module\Gateway\paypal_ppcpv\ModuleFunctionResult\CaptureResult
    {
        return $this->errorReturn("Invalid Invoice ID: " . $invoiceId);
    }
}

?>