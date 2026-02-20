<?php


namespace WHMCS\Module\Gateway\paypal_ppcpv\API;
class RetrieveSetupTokenRequest extends SimpleGetRequest
{
    use RequestAccessTokenAuthenticatedTrait;
    protected $id = "";
    public function send() : HttpResponse
    {
        return $this->partnerAttribution()->acceptJSON()->contentJSON()->get(sprintf("/v3/vault/setup-tokens/%s", $this->id));
    }
    public function sendReady()
    {
        return 0 < strlen($this->id);
    }
    public function responseType() : AbstractResponse
    {
        return new RetrieveSetupTokenResponse();
    }
    public function setSetupTokentIdentifier($setupTokenIdentifier) : \self
    {
        $this->id = $setupTokenIdentifier;
        return $this;
    }
}

?>