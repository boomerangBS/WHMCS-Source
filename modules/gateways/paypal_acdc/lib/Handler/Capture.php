<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Module\Gateway\paypal_acdc\Handler;

class Capture extends AbstractHandler
{
    public function captureInvoice($invoiceId, $payMethod) : \WHMCS\Module\Gateway\paypal_acdc\ModuleFunctionResult\CaptureResult
    {
        $invoice = \WHMCS\Billing\Invoice::findOrFail($invoiceId);
        if($payMethod->gateway_name !== \WHMCS\Module\Gateway\paypal_acdc\Core::MODULE_NAME) {
            throw new \Exception("Invoice gateway does not match module gateway");
        }
        $paymentSource = new \WHMCS\Module\Gateway\paypal_acdc\API\Entity\VaultedCardPaymentSource();
        if(defined("CLIENTAREA")) {
            $paymentSource->setStoredCredentialByType(\WHMCS\Module\Gateway\paypal_acdc\API\Entity\AbstractPaymentSource::CUSTOMER_SUBSEQUENT)->enable3DS();
        } elseif(defined("ADMINAREA") || defined("APICALL")) {
            $paymentSource->setStoredCredentialByType(\WHMCS\Module\Gateway\paypal_acdc\API\Entity\AbstractPaymentSource::MERCHANT_UNSCHEDULED);
        } elseif(defined("IN_CRON")) {
            $paymentSource->setStoredCredentialByType(\WHMCS\Module\Gateway\paypal_acdc\API\Entity\AbstractPaymentSource::MERCHANT_RECURRING);
        }
        $vaultedToken = \WHMCS\Module\Gateway\paypal_acdc\VaultTokenController::factoryModule($this->module)->tokenFromPayMethod($payMethod);
        $paymentSource->setVaultId($vaultedToken->vaultId())->setTransactionIdentifier($vaultedToken->transactionIdentifier());
        $cartCalculatorModel = $invoice->cart();
        $orderResponse = $this->as("WHMCS\\Module\\Gateway\\paypal_acdc\\Handler\\PaymentHandler")->createOrder($paymentSource, $cartCalculatorModel, $invoice);
        return $this->handleResponse($orderResponse, $invoice);
    }
    private function handleResponse(\WHMCS\Module\Gateway\paypal_ppcpv\API\CreateOrderResponse $orderResponse, $invoice) : \WHMCS\Module\Gateway\paypal_acdc\ModuleFunctionResult\CaptureResult
    {
        $result = new \WHMCS\Module\Gateway\paypal_acdc\ModuleFunctionResult\CaptureResult();
        $result->fromApiResponse($orderResponse);
        if($result->isError()) {
            return $result;
        }
        if($orderResponse->isCapturePending()) {
            $invoice->setStatusPending()->save();
            $this->log->historyCapture($orderResponse, $invoice);
        } elseif($orderResponse->isCaptureDeclined()) {
            $this->log->historyCapture($orderResponse, $invoice);
        } elseif($orderResponse->isPayerActionRequired()) {
            $result->setRawData(\WHMCS\Module\Gateway\paypal_ppcpv\Util::decodeJSON($orderResponse->packOrderResponse()))->prependRawData("Reason", \WHMCS\Module\Gateway\paypal_acdc\Logger::THREE_D_SECURE_REQUIRED);
        } elseif($orderResponse->isCaptureComplete()) {
            $captureData = $orderResponse->captureData();
            if($captureData->amount->currency_code != $invoice->getCurrency()["code"]) {
                return $result->setNotSuccessful()->setStatus("error")->setReason("currencyMismatch");
            }
            if(!is_null($orderResponse->paymentVault())) {
                $vaultToken = \WHMCS\Module\Gateway\paypal_acdc\VaultTokenController::factoryModule($this->module)->vaultedTokenFromApiResponse($orderResponse);
                $result->setGatewayId($vaultToken->transformToTokenJSON());
            }
            $result->setRawData(\WHMCS\Module\Gateway\paypal_ppcpv\Util::decodeJSON($orderResponse->packOrderResponse()))->setTransactionId($captureData->id)->setAmount($captureData->amount->value ?? 0)->setFee($captureData->seller_receivable_breakdown->paypal_fee->value ?? 0)->setTransactionHistoryId($this->log->history($orderResponse, $invoice)->id);
            $paymentSource = $orderResponse->paymentSource();
            if($paymentSource instanceof \WHMCS\Module\Gateway\paypal_ppcpv\API\Entity\CardPaymentSourceResponse) {
                $result->setCardDetail($paymentSource->hint(), $paymentSource->expiry(), $paymentSource->brand());
            }
            unset($paymentSource);
        }
        return $result;
    }
}

?>