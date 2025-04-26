<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

namespace WHMCS\Module\Gateway\paypal_ppcpv\API;

// Decoded file for php version 72.
class PaymentCaptureDeclinedEvent extends PaymentCaptureDeniedEvent
{
    public function getHandler() : \WHMCS\Module\Gateway\paypal_ppcpv\Handler\Event\AbstractWebhookHandler
    {
        return new \WHMCS\Module\Gateway\paypal_ppcpv\Handler\Event\PaymentCaptureDeclined();
    }
}

?>