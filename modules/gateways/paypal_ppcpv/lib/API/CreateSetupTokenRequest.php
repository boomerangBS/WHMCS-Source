<?php


namespace WHMCS\Module\Gateway\paypal_ppcpv\API;
class CreateSetupTokenRequest extends AbstractRequest
{
    use RequestAccessTokenAuthenticatedTrait;
    protected $payment_source;
    public function send() : HttpResponse
    {
        return $this->partnerAttribution()->contentJSON()->acceptJSON()->post("/v3/vault/setup-tokens", $this->payload());
    }
    public function payload()
    {
        $payload = [];
        if(!is_null($this->payment_source)) {
            $payload["payment_source"] = $this->payment_source->get();
        }
        return empty($payload) ? "" : json_encode($payload);
    }
    public function sendReady()
    {
        return !empty($this->payment_source);
    }
    public function responseType() : AbstractResponse
    {
        return new CreateSetupTokenResponse();
    }
    public function setPaymentSource(Entity\AbstractPaymentSource $paymentSource) : \self
    {
        $this->payment_source = $paymentSource;
        return $this;
    }
}

?>