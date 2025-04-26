<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

namespace WHMCS\Module\Gateway\paypal_ppcpv\API;

// Decoded file for php version 72.
trait PaymentCaptureEventTrait
{
    public function getInvoiceId() : int
    {
        return (int) $this->invoice_id;
    }
    public function getTransactionId()
    {
        return $this->id;
    }
    public function getAmount()
    {
        if(isset($this->amount->total)) {
            return $this->amount->total;
        }
        if(isset($this->amount->value)) {
            return $this->amount->value;
        }
    }
    public function getCurrentCode()
    {
        if(isset($this->amount->currency_code)) {
            return $this->amount->currency_code;
        }
        if(isset($this->amount->currency)) {
            return $this->amount->currency;
        }
        return "";
    }
    public function getResourceStatus()
    {
        if(isset($this->status)) {
            return strtoupper($this->status);
        }
        if(isset($this->state)) {
            return strtoupper($this->state);
        }
        return "";
    }
    public function isResourceStatusCompleted()
    {
        return $this->getResourceStatus() == "COMPLETED";
    }
    public function hasCardHeuristic(WebhookEventRequest $request) : WebhookEventRequest
    {
        return isset($request->resource->network_transaction_reference->network) || isset($request->resource->processor_response);
    }
}

?>