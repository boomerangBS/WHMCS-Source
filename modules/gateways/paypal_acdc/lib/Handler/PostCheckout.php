<?php

namespace WHMCS\Module\Gateway\paypal_acdc\Handler;

class PostCheckout extends AbstractHandler
{
    public function captureOrder($orderId, $invoice) : \WHMCS\Module\Gateway\paypal_acdc\ModuleFunctionResult\CaptureResult
    {
        $invoice->clearPayMethodId()->save();
        $orderHandler = $this->as("WHMCS\\Module\\Gateway\\paypal_ppcpv\\Handler\\OrderHandler");
        $order = $orderHandler->orderStatus($orderId);
        if(!$order->isLiabilityShifted()) {
            return (new \WHMCS\Module\Gateway\paypal_acdc\ModuleFunctionResult\CaptureResult())->setNotSuccessful()->setStatus("declined")->prependRawData("Reason", \WHMCS\Module\Gateway\paypal_acdc\Logger::THREE_D_SECURE_REQUIRED);
        }
        $cartCalculatorModel = $invoice->cart();
        $cartCurrency = $cartCalculatorModel->total->getCurrency()["code"];
        $cartAmount = (string) \WHMCS\View\Formatter\Price::adjustDecimals($cartCalculatorModel->getTotal()->toNumeric(), $cartCurrency);
        $orderHandler->updateOrder($orderId, $invoice->id, $cartAmount, $cartCurrency);
        $captureResponse = $this->as("WHMCS\\Module\\Gateway\\paypal_acdc\\Handler\\PaymentHandler")->capturePayment($orderId);
        return $this->handleResponse($captureResponse, $invoice);
    }
    private function handleResponse(\WHMCS\Module\Gateway\paypal_ppcpv\API\CapturePaymentResponse $captureResponse, $invoice) : \WHMCS\Module\Gateway\paypal_acdc\ModuleFunctionResult\CaptureResult
    {
        $result = new \WHMCS\Module\Gateway\paypal_acdc\ModuleFunctionResult\CaptureResult();
        $result->fromApiResponse($captureResponse);
        if($result->isError()) {
            return $result;
        }
        if($captureResponse->isCapturePending()) {
            $invoice->setStatusPending()->save();
            $this->log->historyCapture($captureResponse, $invoice);
            throw new \WHMCS\Exception\Gateways\RedirectToInvoice();
        }
        if($captureResponse->isCaptureDeclined()) {
            $result->setRawData(\WHMCS\Module\Gateway\paypal_ppcpv\Util::decodeJSON($captureResponse->packOrderResponse()))->setTransactionHistoryId($this->log->history($captureResponse, $invoice)->id);
        } elseif($captureResponse->isCaptureComplete()) {
            $captureData = $captureResponse->captureData();
            if($captureData->amount->currency_code != $invoice->getCurrency()["code"]) {
                return $result->setNotSuccessful()->setStatus("error")->setReason("currencyMismatch");
            }
            if(!is_null($captureResponse->paymentVault())) {
                $vaultToken = \WHMCS\Module\Gateway\paypal_acdc\VaultTokenController::factoryModule($this->module)->vaultedTokenFromApiResponse($captureResponse);
                $result->setGatewayId($vaultToken->transformToTokenJSON());
            }
            $expiryDate = $captureResponse->paymentSource()->expiry ? \WHMCS\Carbon::createFromFormat("Y-m", $captureResponse->paymentSource()->expiry)->format("m/Y") : "";
            $result->setRawData(\WHMCS\Module\Gateway\paypal_ppcpv\Util::decodeJSON($captureResponse->packOrderResponse()))->setTransactionId($captureData->id)->setAmount($captureData->amount->value ?? 0)->setFee($captureData->seller_receivable_breakdown->paypal_fee->value ?? 0)->setCardDetail($captureResponse->paymentSource()->last_digits ?? "", $expiryDate, $captureResponse->paymentSource()->brand ?? "");
            $result->setTransactionHistoryId($this->log->history($captureResponse, $invoice)->id);
        }
        return $result;
    }
    public function captureInvoice(\WHMCS\Billing\Invoice $invoice) : \WHMCS\Module\Gateway\paypal_acdc\ModuleFunctionResult\CaptureResult
    {
        if(is_null($invoice->payMethod)) {
            throw new \Exception("Invoice pay method not found");
        }
        $captureResult = $this->as("WHMCS\\Module\\Gateway\\paypal_acdc\\Handler\\Capture")->captureInvoice($invoice->id, $invoice->payMethod);
        if($captureResult->is3DSRequired()) {
            $this->as("WHMCS\\Module\\Gateway\\paypal_acdc\\Handler\\ThreeDSecure")->setSessionPayPalThreeDSCheckout($captureResult->getRawApiResponse()->id);
        }
        if($captureResult->isPending()) {
            throw new \WHMCS\Exception\Gateways\RedirectToInvoice();
        }
        return $captureResult;
    }
}

?>