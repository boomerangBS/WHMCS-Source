<?php


namespace WHMCS\Module\Gateway\paypal_ppcpv\API;
class WebhookSignatureVerifyResponse extends AbstractResponse
{
    public $verification_status = "";
    public function getStatus()
    {
        return strtoupper($this->verification_status);
    }
    public function isValid()
    {
        return $this->getStatus() === "SUCCESS";
    }
    public function respond(HttpResponse $response) : AbstractResponse
    {
        return $this->assertHTTPOK($response)->withJSON($response->body);
    }
}

?>