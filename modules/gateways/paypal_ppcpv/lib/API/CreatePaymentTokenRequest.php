<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

namespace WHMCS\Module\Gateway\paypal_ppcpv\API;

// Decoded file for php version 72.
class CreatePaymentTokenRequest extends AbstractRequest
{
    use RequestAccessTokenAuthenticatedTrait;
    protected $payment_source;
    public function send() : HttpResponse
    {
        return $this->partnerAttribution()->contentJSON()->acceptJSON()->post("/v3/vault/payment-tokens", $this->payload());
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
        return new CreatePaymentTokenResponse();
    }
    public function setPaymentSource(Entity\AbstractPaymentSource $paymentSource) : \self
    {
        $this->payment_source = $paymentSource;
        return $this;
    }
}

?>