<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

namespace WHMCS\Module\Gateway\paypal_ppcpv\API;

// Decoded file for php version 72.
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