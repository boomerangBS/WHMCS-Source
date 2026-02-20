<?php


namespace WHMCS\Module\Gateway\paypal_ppcpv\API;
class WebhookSignatureVerifyRequest extends AbstractRequest
{
    use RequestSendReadyAllPropertiesTrait;
    use RequestAccessTokenAuthenticatedTrait;
    protected $authAlgo = "";
    protected $certUrl = "";
    protected $transmissionId = "";
    protected $transmissionSig = "";
    protected $transmissionTime = "";
    protected $webhookId = "";
    protected $webhookEvent = "";
    public function setAuthAlgo($authAlgo) : \self
    {
        $this->assertStringNotEmpty("authAlgo", $authAlgo);
        $this->authAlgo = $authAlgo;
        return $this;
    }
    public function setCertUrl($certUrl) : \self
    {
        $this->assertStringNotEmpty("certURL", $certUrl);
        $this->certUrl = $certUrl;
        return $this;
    }
    public function setTransmission($id, string $signature, string $time) : \self
    {
        $this->assertStringNotEmpty("transmission", $id, $signature, $time);
        $this->transmissionId = $id;
        $this->transmissionSig = $signature;
        $this->transmissionTime = $time;
        return $this;
    }
    public function setWebhookId($webhookId) : \self
    {
        $this->assertStringNotEmpty("webhook identifier", $webhookId);
        $this->webhookId = $webhookId;
        return $this;
    }
    public function setEventBody($eventJSON) : \self
    {
        $this->assertStringNotEmpty("event body", $eventJSON);
        $this->webhookEvent = $eventJSON;
        return $this;
    }
    protected function assertStringNotEmpty($label, string $values) : void
    {
        foreach ($values as $value) {
            if(strlen($value) == 0) {
                throw new \InvalidArgumentException($label);
            }
        }
    }
    public function send() : HttpResponse
    {
        return $this->partnerAttribution()->acceptJSON()->contentJSON()->post("/v1/notifications/verify-webhook-signature", $this->payload());
    }
    protected function payload()
    {
        return json_encode(["auth_algo" => $this->authAlgo, "cert_url" => $this->certUrl, "transmission_id" => $this->transmissionId, "transmission_sig" => $this->transmissionSig, "transmission_time" => $this->transmissionTime, "webhook_id" => $this->webhookId, "webhook_event" => json_decode($this->webhookEvent)]);
    }
    public function responseType() : AbstractResponse
    {
        return new WebhookSignatureVerifyResponse();
    }
}

?>