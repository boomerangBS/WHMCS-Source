<?php


namespace WHMCS\Module\Gateway\paypal_ppcpv\API;
class PaymentCaptureLookupRequest extends SimpleGetRequest
{
    use RequestAccessTokenAuthenticatedTrait;
    protected $transactionIdentifier = "";
    public function send() : HttpResponse
    {
        return $this->partnerAttribution()->acceptJSON()->contentJSON()->get(sprintf("/v2/payments/captures/%s", $this->transactionIdentifier));
    }
    public function sendReady()
    {
        return 0 < strlen($this->transactionIdentifier);
    }
    public function responseType() : AbstractResponse
    {
        return new PaymentCaptureLookupResponse();
    }
    public function setTransactionIdentifier($transactionIdentifier) : \self
    {
        $this->transactionIdentifier = $transactionIdentifier;
        return $this;
    }
}

?>