<?php


namespace WHMCS\Module\Gateway\paypal_ppcpv\API;
class ListWebhookRequest extends SimpleGetRequest
{
    use RequestSendReadyAllPropertiesTrait;
    use RequestAccessTokenAuthenticatedTrait;
    public function send() : HttpResponse
    {
        return $this->partnerAttribution()->acceptJSON()->contentJSON()->get("/v1/notifications/webhooks");
    }
    public function responseType() : AbstractResponse
    {
        return new ListWebhookResponse();
    }
}

?>