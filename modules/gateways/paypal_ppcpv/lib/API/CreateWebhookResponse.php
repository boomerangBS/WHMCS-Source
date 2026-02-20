<?php


namespace WHMCS\Module\Gateway\paypal_ppcpv\API;
class CreateWebhookResponse extends AbstractResponse
{
    public $id;
    public $url;
    public $event_types;
    public $links;
    public function id()
    {
        return $this->id;
    }
    public function respond(HttpResponse $response) : AbstractResponse
    {
        return $this->assertHTTPSuccess($response)->withJSON($response->body);
    }
}

?>