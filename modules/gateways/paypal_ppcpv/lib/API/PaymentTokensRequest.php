<?php


namespace WHMCS\Module\Gateway\paypal_ppcpv\API;
class PaymentTokensRequest extends SimpleGetRequest
{
    use RequestAccessTokenAuthenticatedTrait;
    public $customerId = "";
    public function customerId($customerId) : \self
    {
        $this->customerId = $customerId;
        return $this;
    }
    public function send() : HttpResponse
    {
        return $this->partnerAttribution()->acceptJSON()->contentJSON()->get(sprintf("/v3/vault/payment-tokens?customer_id=%s", $this->customerId));
    }
    public function sendReady()
    {
        return 0 < strlen($this->customerId);
    }
    public function responseType() : AbstractResponse
    {
        return new PaymentTokensResponse();
    }
}

?>