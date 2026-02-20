<?php

namespace WHMCS\Module\Gateway\paypal_ppcpv\Handler;

class PaymentHandler extends AbstractHandler
{
    public function createOrder(\WHMCS\Module\Gateway\paypal_ppcpv\API\Entity\AbstractPaymentSource $paymentSource, $cartCalculatorModel = NULL, $invoice) : \WHMCS\Module\Gateway\paypal_ppcpv\API\CreateOrderResponse
    {
        $companyName = \WHMCS\Config\Setting::getValue("CompanyName");
        $cartCurrency = $cartCalculatorModel->total->getCurrency()["code"];
        $cartAmount = (string) \WHMCS\View\Formatter\Price::adjustDecimals($cartCalculatorModel->getTotal()->toNumeric(), $cartCurrency);
        $orderDescription = self::makeOrderDescription($companyName, $invoice);
        $purchaseReference = is_null($invoice) ? md5(session_id() . time()) : $invoice->id;
        $api = $this->api();
        $createOrderRequest = (new \WHMCS\Module\Gateway\paypal_ppcpv\API\CreateOrderRequest($api))->setAsCapture()->setPaymentSource($paymentSource)->setPurchaseUnit($orderDescription, $purchaseReference, $cartAmount, $cartCurrency);
        $createOrderResponse = $api->send($createOrderRequest);
        if(!$createOrderResponse instanceof \WHMCS\Module\Gateway\paypal_ppcpv\API\CreateOrderResponse) {
            throw new \Exception($createOrderResponse->__toString());
        }
        return $createOrderResponse;
    }
    public function invoiceOnApprove($invoiceId, string $orderId) : array
    {
        $payMethod = NULL;
        $result = new \WHMCS\Module\Gateway\paypal_ppcpv\ModuleFunctionResult\CaptureResult();
        $invoiceModel = \WHMCS\Billing\Invoice::find($invoiceId);
        if(is_null($invoiceModel)) {
            return [$result->setNotSuccessful()->setRedirectUrl(routePath("clientarea-home")), NULL];
        }
        try {
            $api = $this->api();
            $capturePaymentResponse = $api->send((new \WHMCS\Module\Gateway\paypal_ppcpv\API\CapturePaymentRequest($api))->setId($orderId));
            if(!$capturePaymentResponse instanceof \WHMCS\Module\Gateway\paypal_ppcpv\API\CapturePaymentResponse) {
                throw new \Exception($capturePaymentResponse->__toString());
            }
        } catch (\Exception $e) {
            return [$result->setNotSuccessful()->setReason($e->__toString()), NULL];
        }
        $this->log->historyCapture($capturePaymentResponse, $invoiceModel);
        $result->fromApiResponse($capturePaymentResponse);
        $baseUrl = \App::getSystemURL();
        if($capturePaymentResponse->isCapturePending()) {
            $result->setRedirectUrl($baseUrl . "viewinvoice.php?id=" . $invoiceModel->id . "&paymentinititated=true");
            $invoiceModel->setStatusPending()->save();
        } elseif($capturePaymentResponse->isCaptureDeclined()) {
        } elseif($capturePaymentResponse->isCaptureComplete()) {
            $result->setRedirectUrl($baseUrl . "viewinvoice.php?id=" . $invoiceModel->id . "&paymentsuccess=true");
            $payMethod = NULL;
            if(!is_null($capturePaymentResponse->paymentVault())) {
                $vaultController = \WHMCS\Module\Gateway\paypal_ppcpv\VaultTokenController::factoryModule($this->module);
                $vaultToken = $vaultController->vaultedTokenFromCapturePayment($capturePaymentResponse);
                if(!is_null($vaultToken)) {
                    $payMethod = $vaultController->saveVaultedToken($invoiceModel->client, $vaultToken);
                }
                unset($vaultToken);
                unset($vaultController);
            }
            $captureData = $capturePaymentResponse->captureData();
            $invoiceModel->addPayment($captureData->amount->value, $captureData->id, $captureData->seller_receivable_breakdown->paypal_fee->value, $this->module->getLoadedModule());
            if(!is_null($payMethod)) {
                $invoiceModel->setPaymentMethod($this->module->getLoadedModule());
                $invoiceModel->payMethod()->associate($payMethod);
                $invoiceModel->save();
            }
        }
        return [$result, $payMethod];
    }
    public function capturePayment($orderId = NULL, $paymentSource) : \WHMCS\Module\Gateway\paypal_ppcpv\API\CapturePaymentResponse
    {
        $api = $this->api();
        $capturePaymentRequest = (new \WHMCS\Module\Gateway\paypal_ppcpv\API\CapturePaymentRequest($api))->setId($orderId);
        if(!is_null($paymentSource)) {
            $capturePaymentRequest->withPaymentSource($paymentSource);
        }
        $capturePaymentResponse = $api->send($capturePaymentRequest);
        if(!$capturePaymentResponse instanceof \WHMCS\Module\Gateway\paypal_ppcpv\API\CapturePaymentResponse) {
            throw new \Exception($capturePaymentResponse->__toString());
        }
        return $capturePaymentResponse;
    }
    public function createSetupToken(\WHMCS\Module\Gateway\paypal_ppcpv\API\Entity\AbstractPaymentSource $paymentSource) : \WHMCS\Module\Gateway\paypal_ppcpv\API\CreateSetupTokenResponse
    {
        $api = $this->api();
        $createSetupTokenResponse = $api->send((new \WHMCS\Module\Gateway\paypal_ppcpv\API\CreateSetupTokenRequest($api))->setPaymentSource($paymentSource));
        if(!$createSetupTokenResponse instanceof \WHMCS\Module\Gateway\paypal_ppcpv\API\CreateSetupTokenResponse) {
            throw new \Exception($createSetupTokenResponse->__toString());
        }
        return $createSetupTokenResponse;
    }
    public function getSetupToken($setupToken) : \WHMCS\Module\Gateway\paypal_ppcpv\API\RetrieveSetupTokenResponse
    {
        $api = $this->api();
        $retrieveSetupTokenResponse = $api->send((new \WHMCS\Module\Gateway\paypal_ppcpv\API\RetrieveSetupTokenRequest($api))->setSetupTokentIdentifier($setupToken));
        if(!$retrieveSetupTokenResponse instanceof \WHMCS\Module\Gateway\paypal_ppcpv\API\RetrieveSetupTokenResponse) {
            throw new \Exception($retrieveSetupTokenResponse->__toString());
        }
        return $retrieveSetupTokenResponse;
    }
    public function createPaymentToken(\WHMCS\Module\Gateway\paypal_acdc\API\Entity\SetupTokenPaymentSource $paymentSource) : \WHMCS\Module\Gateway\paypal_ppcpv\API\CreatePaymentTokenResponse
    {
        $api = $this->api();
        $createPaymentTokenResponse = $api->send((new \WHMCS\Module\Gateway\paypal_ppcpv\API\CreatePaymentTokenRequest($api))->setPaymentSource($paymentSource));
        if(!$createPaymentTokenResponse instanceof \WHMCS\Module\Gateway\paypal_ppcpv\API\CreatePaymentTokenResponse) {
            throw new \Exception($createPaymentTokenResponse->__toString());
        }
        return $createPaymentTokenResponse;
    }
    public static function makeOrderDescription($companyName = NULL, $invoiceModel) : \WHMCS\Billing\Invoice
    {
        if(isset($invoiceModel)) {
            return $companyName . " - Invoice # " . $invoiceModel->getInvoiceNumber();
        }
        return $companyName . " Shopping Cart Checkout";
    }
}

?>