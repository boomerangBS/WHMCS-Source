<?php


namespace WHMCS\Module\Gateway\paypal_ppcpv\API;
class DeleteWebhookRequest extends SimpleGetRequest
{
    use RequestSendReadyAllPropertiesTrait;
    use RequestAccessTokenAuthenticatedTrait;
    protected $webhook_id;
    public function setWebhookId($webhookid)
    {
        $this->webhook_id = $webhookid;
        return $this;
    }
    public function send() : HttpResponse
    {
        return $this->partnerAttribution()->acceptJSON()->contentJSON()->delete("/v1/notifications/webhooks/" . $this->webhook_id);
    }
    public function responseType() : AbstractResponse
    {
        return new DeleteWebhookResponse();
    }
}

?>