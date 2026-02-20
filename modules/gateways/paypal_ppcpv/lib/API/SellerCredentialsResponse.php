<?php


namespace WHMCS\Module\Gateway\paypal_ppcpv\API;
class SellerCredentialsResponse extends AbstractResponse
{
    public $client_id = "";
    public $client_secret = "";
    public $payer_id = "";
    public function clientId()
    {
        return $this->client_id;
    }
    public function clientSecret()
    {
        return $this->client_secret;
    }
    public function payerId()
    {
        return $this->payer_id;
    }
    public function respond(HttpResponse $response) : AbstractResponse
    {
        return $this->assertHTTPOK($response)->withJSON($response->body);
    }
}

?>