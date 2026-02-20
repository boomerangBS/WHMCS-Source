<?php


namespace WHMCS\Module\Gateway\paypal_ppcpv\API;
class DeletePaymentTokenRequest extends SimpleGetRequest
{
    use RequestSendReadyAllPropertiesTrait;
    use RequestAccessTokenAuthenticatedTrait;
    protected $vaultId = "";
    public function setVaultId($vaultId) : \self
    {
        $this->vaultId = $vaultId;
        return $this;
    }
    public function send() : HttpResponse
    {
        return $this->partnerAttribution()->acceptJSON()->contentJSON()->delete("/v3/vault/payment-tokens/" . $this->vaultId);
    }
    public function responseType() : AbstractResponse
    {
        return new SuccessNoContentResponse();
    }
}

?>