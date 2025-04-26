<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

namespace WHMCS\Module\Gateway\paypal_ppcpv\API;

// Decoded file for php version 72.
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