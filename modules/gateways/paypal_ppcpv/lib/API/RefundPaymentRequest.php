<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

namespace WHMCS\Module\Gateway\paypal_ppcpv\API;

// Decoded file for php version 72.
class RefundPaymentRequest extends AbstractRequest
{
    use RequestAccessTokenAuthenticatedTrait;
    protected $invoiceId;
    protected $transactionId;
    protected $amountValue;
    protected $amountCurrencyCode;
    public function send() : HttpResponse
    {
        return $this->partnerAttribution()->acceptJSON()->contentJSON()->post("/v2/payments/captures/" . $this->transactionId . "/refund", $this->payload());
    }
    public function sendReady()
    {
        return !empty($this->transactionId);
    }
    public function payload()
    {
        return json_encode(["amount" => ["value" => $this->amountValue, "currency_code" => $this->amountCurrencyCode], "invoice_id" => $this->invoiceId]);
    }
    public function responseType() : AbstractResponse
    {
        return new RefundPaymentResponse();
    }
    public function setInvoiceId($invoiceId) : \self
    {
        $this->invoiceId = $invoiceId;
        return $this;
    }
    public function setTransactionId($transactionId) : \self
    {
        $this->transactionId = $transactionId;
        return $this;
    }
    public function setAmount($amountValue, string $currencyCode) : \self
    {
        $this->amountValue = $amountValue;
        $this->amountCurrencyCode = $currencyCode;
        return $this;
    }
}

?>