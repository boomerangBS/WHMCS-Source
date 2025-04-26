<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

namespace WHMCS\Module\Gateway\paypal_ppcpv\API;

// Decoded file for php version 72.
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