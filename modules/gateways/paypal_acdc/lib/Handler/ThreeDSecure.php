<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Module\Gateway\paypal_acdc\Handler;

class ThreeDSecure extends AbstractHandler
{
    const MODULE_FUNCTION_DECLINED = "declined";
    const MODULE_FUNCTION_SUCCESS = "success";
    public function handle($hashedOrderIdentifier) : array
    {
        $captureResult = NULL;
        $orderIdentifier = $this->getAndDeleteSessionPayPalThreeDSChallenge();
        $this->assertValidThreeDSChallenge($hashedOrderIdentifier, $orderIdentifier);
        $orderStatus = $this->as("WHMCS\\Module\\Gateway\\paypal_ppcpv\\Handler\\OrderHandler")->orderStatus($orderIdentifier);
        $invoice = $this->assertValidInvoice($orderStatus->invoiceIdentifier());
        $threeDSecureResponse = $orderStatus->threeDSecureResponse();
        if($threeDSecureResponse->isApproved()) {
            list($captureResult) = $this->as("WHMCS\\Module\\Gateway\\paypal_acdc\\Handler\\PaymentHandler")->captureOrder($orderStatus->id, $invoice->id);
            if($captureResult->isComplete() || $captureResult->isCapturePending()) {
                $this->setSessionInvoicePaymentComplete($invoice->id);
            }
        } else {
            $this->log->orderDeclineLiability($orderStatus);
        }
        return [$captureResult, $invoice];
    }
    public function byInvoiceIdentifier($invoiceId, $paymentMethod) : int
    {
        return $this->threeDSecure($invoiceId, $this->as("WHMCS\\Module\\Gateway\\paypal_acdc\\Handler\\Capture")->captureInvoice($invoiceId, $paymentMethod));
    }
    public function byOrderIdentifier($orderIdentifier)
    {
        $this->setSessionPayPalThreeDSChallenge($orderIdentifier);
        return $this->threeDSForm($orderIdentifier, $this->threeDSActionUrl($orderIdentifier));
    }
    protected function threeDSecure($invoiceId, $captureResult) : int
    {
        $orderResponse = $captureResult->getRawApiResponse();
        if($orderResponse->isCaptureComplete()) {
            $invoiceModel = \WHMCS\Billing\Invoice::find($invoiceId);
            if(is_null($invoiceModel)) {
                throw new \WHMCS\Exception\Gateways\RedirectToInvoice("Invoice Not Found");
            }
            $invoiceModel->addPayment($orderResponse->captureData()->amount->value, $orderResponse->captureData()->id, $orderResponse->captureData()->seller_receivable_breakdown->paypal_fee->value, $this->module->getLoadedModule());
            $this->log->gatewayCapture($orderResponse, \WHMCS\Billing\Payment\Transaction\History::find($captureResult->getTransactionHistoryId()));
            $this->setSessionInvoicePaymentComplete($invoiceModel->id);
            return self::MODULE_FUNCTION_SUCCESS;
        }
        if($captureResult->is3DSRequired()) {
            $captureData = $captureResult->getRawApiResponse();
            if(is_null($captureData)) {
                throw new \Exception("Order Response Not Found");
            }
            $paypal3DSURL = $captureData->link("payer-action")->href;
            if(strlen($paypal3DSURL) == 0) {
                throw new \WHMCS\Exception\Gateways\RedirectToInvoice();
            }
            $this->setSessionPayPalThreeDSChallenge($captureData->id);
            return $this->threeDSForm($captureData->id, $paypal3DSURL);
        }
        if($captureResult->isPending()) {
            return self::MODULE_FUNCTION_SUCCESS;
        }
        return self::MODULE_FUNCTION_DECLINED;
    }
    public static function frameBreakout($redirectPage, string $challenge)
    {
        return moduleView("paypal_acdc", "threeds.post-to-parent", ["redirectPage" => $redirectPage, "challenge" => $challenge]);
    }
    public function invoiceCaptureSuccessUrl($invoiceId) : int
    {
        return $this->systemConfiguration->app()->getRedirectUrl(sprintf("%s%s", $this->systemConfiguration->app()->getSystemURL(), "viewinvoice.php"), ["id" => $invoiceId, "paymentsuccess" => true]);
    }
    public function invoiceCaptureFailureUrl($invoiceId) : int
    {
        return $this->systemConfiguration->app()->getRedirectUrl(sprintf("%s%s", $this->systemConfiguration->app()->getSystemURL(), "viewinvoice.php"), ["id" => $invoiceId, "paymentfailed" => true]);
    }
    protected function getAndDeleteSessionPayPalThreeDSChallenge()
    {
        return $this->getAndDeleteSessionPayPalThreeDSOrderIdentifier("c");
    }
    protected function setSessionPayPalThreeDSChallenge($orderIdentifier) : void
    {
        $this->setSessionPayPalThreeDS($orderIdentifier, "c");
    }
    public function getAndDeleteSessionPayPalThreeDSCheckout()
    {
        return $this->getAndDeleteSessionPayPalThreeDSOrderIdentifier("o");
    }
    protected function sessionPayPalThreeDS()
    {
        return sprintf("%s_3ds", \WHMCS\Module\Gateway\paypal_acdc\Core::MODULE_NAME);
    }
    public function setSessionPayPalThreeDSCheckout($orderIdentifier) : void
    {
        $this->setSessionPayPalThreeDS($orderIdentifier, "o");
    }
    protected function setSessionPayPalThreeDS($orderIdentifier, string $stage) : void
    {
        \WHMCS\Session::set($this->sessionPayPalThreeDS(), $stage . ":" . $orderIdentifier);
    }
    protected function getAndDeleteSessionPayPalThreeDSOrderIdentifier($stage)
    {
        $data = \WHMCS\Session::get($this->sessionPayPalThreeDS());
        if(strlen($data) == 0) {
            return "";
        }
        $pattern = "/^" . $stage . ":\\s*(\\S+)/";
        if(preg_match($pattern, $data, $matches)) {
            \WHMCS\Session::delete($this->sessionPayPalThreeDS());
            return $matches[1];
        }
        return "";
    }
    protected function hashOrderIdentifier($orderIdentifier)
    {
        return sha1($orderIdentifier);
    }
    private function threeDsForm($orderIdentifier, string $paypal3DSURL)
    {
        return moduleView("paypal_acdc", "threeds.iframe-script", ["actionURL" => sprintf("%s&redirect_uri=%s", $paypal3DSURL, urlencode($this->callbackUrl($orderIdentifier)))]);
    }
    private function callbackUrl($orderIdentifier)
    {
        return sprintf("%s%s?hash=%s", $this->systemConfiguration->app()->getSystemURL(), "modules/gateways/callback/paypal_acdc_3ds.php", $this->hashOrderIdentifier($orderIdentifier));
    }
    private function threeDSActionUrl($orderIdentifier)
    {
        return sprintf("%s/webapps/helios?action=verify&flow=3ds&cart_id=%s", $this->env()->webURL, urlencode($orderIdentifier));
    }
    private function setSessionInvoicePaymentComplete(int $invoiceId)
    {
        if(!\WHMCS\Session::exists("orderdetails")) {
            return NULL;
        }
        $orderDetails = \WHMCS\Session::get("orderdetails");
        if($invoiceId !== $orderDetails["InvoiceID"]) {
            return NULL;
        }
        $orderDetails["paymentcomplete"] = true;
        \WHMCS\Session::set("orderdetails", $orderDetails);
    }
    private function assertValidInvoice($invoiceId) : \WHMCS\Billing\Invoice
    {
        $invoice = \WHMCS\Billing\Invoice::find($invoiceId);
        if(is_null($invoice)) {
            throw new \WHMCS\Exception\Module\NotServicable("Invoice #" . $invoiceId . " Not Found");
        }
        return $invoice;
    }
    private function assertValidThreeDSChallenge(string $challengeValue, string $orderIdentifier)
    {
        if(strlen($orderIdentifier) == 0 || $challengeValue !== $this->hashOrderIdentifier($orderIdentifier)) {
            throw new \WHMCS\Exception\Module\NotServicable("Order Not Found");
        }
    }
}

?>