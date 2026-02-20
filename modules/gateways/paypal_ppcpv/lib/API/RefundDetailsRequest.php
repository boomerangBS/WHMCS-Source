<?php


namespace WHMCS\Module\Gateway\paypal_ppcpv\API;
class RefundDetailsRequest extends SimpleGetRequest
{
    use RequestAccessTokenAuthenticatedTrait;
    protected $refundIdentifier = "";
    public function send() : HttpResponse
    {
        return $this->acceptJSON()->get("/v2/payments/refunds/" . $this->refundIdentifier);
    }
    public function responseType() : AbstractResponse
    {
        return new RefundDetailsResponse();
    }
    public function setRefundIdentifier($refundIdentifier) : \self
    {
        $this->refundIdentifier = $refundIdentifier;
        return $this;
    }
}

?>