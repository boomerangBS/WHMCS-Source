<?php


namespace WHMCS\Module\Gateway\paypal_ppcpv\API;
class SellerCredentialsRequest extends SimpleGetRequest
{
    use RequestSendReadyAllPropertiesTrait;
    protected $token = "";
    public function send() : HttpResponse
    {
        return $this->partnerAttribution()->acceptJSON()->contentURLEncoded()->bearerAuthorization($this->token)->get(sprintf("/v1/customer/partners/%s/merchant-integrations/credentials", $this->env()->partnerId));
    }
    public function responseType() : AbstractResponse
    {
        return new SellerCredentialsResponse();
    }
    public function setToken($token) : \self
    {
        $this->token = $token;
        return $this;
    }
}

?>