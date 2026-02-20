<?php

namespace WHMCS\Module\Gateway\paypal_ppcpv\Handler;

class PostCheckout extends AbstractHandler
{
    public function captureOrder(string $orderId, \WHMCS\Billing\Invoice $invoice)
    {
        $invoice->clearPayMethodId()->save();
        $orderHandler = $this->as("WHMCS\\Module\\Gateway\\paypal_ppcpv\\Handler\\OrderHandler");
        $orderStatusResponse = $orderHandler->orderStatus($orderId);
        $cartCalculatorModel = $invoice->cart();
        $cartCurrency = $cartCalculatorModel->total->getCurrency()["code"];
        $cartAmount = (string) \WHMCS\View\Formatter\Price::adjustDecimals($cartCalculatorModel->getTotal()->toNumeric(), $cartCurrency);
        $orderHandler->updateOrder($orderId, $invoice->id, $cartAmount, $cartCurrency);
        $captureResponse = $this->as("WHMCS\\Module\\Gateway\\paypal_ppcpv\\Handler\\PaymentHandler")->capturePayment($orderId);
        return $this->handleResponse($captureResponse, $invoice);
    }
    private function handleResponse(\WHMCS\Module\Gateway\paypal_ppcpv\API\CapturePaymentResponse $capturePaymentResponse, $invoiceModel) : \WHMCS\Module\Gateway\paypal_ppcpv\ModuleFunctionResult\CaptureResult
    {
        $result = new \WHMCS\Module\Gateway\paypal_ppcpv\ModuleFunctionResult\CaptureResult();
        $result->fromApiResponse($capturePaymentResponse);
        if($result->isError()) {
            return $result;
        }
        if($capturePaymentResponse->isCapturePending()) {
            $invoiceModel->setStatusPending()->save();
            $this->log->historyCapture($capturePaymentResponse, $invoiceModel);
            throw new \WHMCS\Exception\Gateways\RedirectToInvoice();
        }
        if($capturePaymentResponse->isCaptureDeclined()) {
            $result->setRawData(\WHMCS\Module\Gateway\paypal_ppcpv\Util::decodeJSON($capturePaymentResponse->packOrderResponse()))->setTransactionHistoryId($this->log->history($capturePaymentResponse, $invoiceModel)->id);
        } elseif($capturePaymentResponse->isCaptureComplete()) {
            $captureData = $capturePaymentResponse->captureData();
            if($captureData->amount->currency_code != $invoiceModel->getCurrency()["code"]) {
                return $result->setNotSuccessful()->setStatus("error")->setReason("currencyMismatch");
            }
            $result->setRawData(\WHMCS\Module\Gateway\paypal_ppcpv\Util::decodeJSON($capturePaymentResponse->packOrderResponse()))->setTransactionId($captureData->id)->setAmount($captureData->amount->value ?? 0)->setFee($captureData->seller_receivable_breakdown->paypal_fee->value ?? 0)->setTransactionHistoryId($this->log->history($capturePaymentResponse, $invoiceModel)->id);
            if(!is_null($capturePaymentResponse->paymentVault())) {
                $vaultToken = \WHMCS\Module\Gateway\paypal_ppcpv\VaultTokenController::factoryModule($this->module)->vaultedTokenFromCapturePayment($capturePaymentResponse);
                if(!is_null($vaultToken)) {
                    $result->setGatewayId($vaultToken->transformToTokenJSON());
                    $result->setPayPalDetail($vaultToken);
                }
                unset($vaultToken);
            }
        }
        return $result;
    }
    public function captureInvoice(\WHMCS\Billing\Invoice $invoice)
    {
        $module = \WHMCS\Module\Gateway\paypal_ppcpv\PayPalCommerce::loadModule();
        $paypalToken = \WHMCS\Module\Gateway\paypal_ppcpv\VaultTokenController::factoryModule($module)->tokenFromPayMethod($invoice->payMethod);
        $paymentSource = (new \WHMCS\Module\Gateway\paypal_ppcpv\API\Entity\VaultedPaypalPaymentSource())->withVaultedToken($paypalToken);
        $captureResult = $this->as("WHMCS\\Module\\Gateway\\paypal_ppcpv\\Handler\\Capture")->handle($invoice, $paymentSource);
        if($captureResult->isPending()) {
            throw new \WHMCS\Exception\Gateways\RedirectToInvoice();
        }
        return $captureResult;
    }
}

?>