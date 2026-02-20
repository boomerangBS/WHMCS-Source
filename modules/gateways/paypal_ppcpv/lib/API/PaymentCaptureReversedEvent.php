<?php


namespace WHMCS\Module\Gateway\paypal_ppcpv\API;
class PaymentCaptureReversedEvent extends PaymentCaptureRefundedEvent
{
    public function getHandler() : \WHMCS\Module\Gateway\paypal_ppcpv\Handler\Event\AbstractWebhookHandler
    {
        return new \WHMCS\Module\Gateway\paypal_ppcpv\Handler\Event\PaymentCaptureReversed();
    }
}

?>