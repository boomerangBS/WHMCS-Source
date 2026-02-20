<?php


namespace WHMCS\Module\Gateway\paypal_ppcpv\API;
class DeleteWebhookResponse extends AbstractResponse
{
    public $webhooks;
    public function webhooks() : array
    {
        return $this->webhooks;
    }
    public function respond(HttpResponse $response) : AbstractResponse
    {
        return $this->assertHTTPStatus($response, 204);
    }
}

?>