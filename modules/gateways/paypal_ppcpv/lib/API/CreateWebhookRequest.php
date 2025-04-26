<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

namespace WHMCS\Module\Gateway\paypal_ppcpv\API;

// Decoded file for php version 72.
class CreateWebhookRequest extends AbstractRequest
{
    use RequestSendReadyAllPropertiesTrait;
    use RequestAccessTokenAuthenticatedTrait;
    protected $eventTypes = [];
    protected $url = "";
    public function setEventsTypes($eventTypes) : \self
    {
        $this->eventTypes = $eventTypes;
        return $this;
    }
    public function setUrl($url) : \self
    {
        $this->url = $url;
        return $this;
    }
    public function send() : HttpResponse
    {
        return $this->partnerAttribution()->acceptJSON()->contentJSON()->post("/v1/notifications/webhooks", $this->payload());
    }
    protected function payload()
    {
        return json_encode(["url" => $this->url, "event_types" => array_map(function ($value) {
            return ["name" => $value];
        }, $this->eventTypes)]);
    }
    public function responseType() : AbstractResponse
    {
        return new CreateWebhookResponse();
    }
}

?>