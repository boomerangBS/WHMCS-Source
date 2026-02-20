<?php


namespace WHMCS\Module\Gateway\paypal_ppcpv\API;
class PaymentCaptureDeniedEvent extends PaymentCapturePendingEvent
{
    public function getHandler() : \WHMCS\Module\Gateway\paypal_ppcpv\Handler\Event\AbstractWebhookHandler
    {
        return new \WHMCS\Module\Gateway\paypal_ppcpv\Handler\Event\PaymentCaptureDenied();
    }
    public function initiatingModule()
    {
        if(isset($this->request->resource->processor_response)) {
            return \WHMCS\Module\Gateway\paypal_acdc\Core::MODULE_NAME;
        }
        return \WHMCS\Module\Gateway\paypal_ppcpv\PayPalCommerce::MODULE_NAME;
    }
}

?>