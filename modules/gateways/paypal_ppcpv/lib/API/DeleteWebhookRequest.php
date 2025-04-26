<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

namespace WHMCS\Module\Gateway\paypal_ppcpv\API;

// Decoded file for php version 72.
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