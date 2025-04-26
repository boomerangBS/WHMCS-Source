<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

namespace WHMCS\Module\Gateway\paypal_ppcpv\API;

// Decoded file for php version 72.
class PaymentCaptureCompletedEvent extends AbstractWebhookEvent
{
    use PaymentCaptureEventTrait;
    public $amount;
    public $id = "";
    public $status = "";
    public $invoice_id = "";
    public $seller_receivable_breakdown;
    protected $expectedPayloadProperties = ["id", "status", "invoice_id", "amount->value", "amount->currency_code", "seller_receivable_breakdown->paypal_fee->value"];
    public function getSellerFee()
    {
        return $this->seller_receivable_breakdown->paypal_fee->value;
    }
    public function getHandler() : \WHMCS\Module\Gateway\paypal_ppcpv\Handler\Event\AbstractWebhookHandler
    {
        if($this->getResourceStatus() == "PENDING") {
            return new \WHMCS\Module\Gateway\paypal_ppcpv\Handler\Event\PaymentCapturePending();
        }
        return new \WHMCS\Module\Gateway\paypal_ppcpv\Handler\Event\PaymentCaptureCompleted();
    }
    public function initiatingModule()
    {
        if($this->hasCardHeuristic($this->request)) {
            return \WHMCS\Module\Gateway\paypal_acdc\Core::MODULE_NAME;
        }
        return \WHMCS\Module\Gateway\paypal_ppcpv\PayPalCommerce::MODULE_NAME;
    }
}

?>