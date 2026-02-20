<?php


namespace WHMCS\Module\Gateway\paypal_ppcpv\API;
class PaymentCapturePendingEvent extends AbstractWebhookEvent
{
    use PaymentCaptureEventTrait;
    public $amount;
    public $id = "";
    public $status = "";
    public $status_details;
    public $invoice_id = "";
    protected $expectedPayloadProperties = ["id", "invoice_id", "status", "amount->value", "amount->currency_code"];
    public function getHandler() : \WHMCS\Module\Gateway\paypal_ppcpv\Handler\Event\AbstractWebhookHandler
    {
        return new \WHMCS\Module\Gateway\paypal_ppcpv\Handler\Event\PaymentCapturePending();
    }
    public function initiatingModule()
    {
        if($this->hasCardHeuristic($this->request)) {
            return \WHMCS\Module\Gateway\paypal_acdc\Core::MODULE_NAME;
        }
        return \WHMCS\Module\Gateway\paypal_ppcpv\PayPalCommerce::MODULE_NAME;
    }
    public function getStatusDetailReason()
    {
        return $this->status_details->reason ?? "";
    }
}

?>