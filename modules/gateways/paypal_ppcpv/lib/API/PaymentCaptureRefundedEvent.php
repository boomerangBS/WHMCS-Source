<?php


namespace WHMCS\Module\Gateway\paypal_ppcpv\API;
class PaymentCaptureRefundedEvent extends AbstractWebhookEvent
{
    use PaymentCaptureEventTrait;
    public $amount;
    public $id = "";
    public $status = "";
    public $invoice_id = "";
    public $seller_payable_breakdown;
    protected $moduleName;
    protected $expectedPayloadProperties = ["id", "invoice_id", "status", "amount->value", "amount->currency_code"];
    public function getHandler() : \WHMCS\Module\Gateway\paypal_ppcpv\Handler\Event\AbstractWebhookHandler
    {
        return new \WHMCS\Module\Gateway\paypal_ppcpv\Handler\Event\PaymentCaptureRefunded();
    }
    public function sellerPayableBreakdown()
    {
        return $this->seller_payable_breakdown;
    }
    public function capturedTransactionIdentifier()
    {
        $captureTransactionId = "";
        $captureLink = $this->getLinkByRelation("up");
        if(!is_null($captureLink)) {
            $captureTransactionId = basename($captureLink);
        }
        return $captureTransactionId;
    }
    public function initiatingModule()
    {
        if(is_null($this->moduleName)) {
            $this->moduleName = $this->getHandler()->determineInitializingModule($this->getInvoiceId(), $this->capturedTransactionIdentifier());
        }
        return $this->moduleName;
    }
}

?>