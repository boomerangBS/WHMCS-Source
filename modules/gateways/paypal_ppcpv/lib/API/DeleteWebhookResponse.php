<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

namespace WHMCS\Module\Gateway\paypal_ppcpv\API;

// Decoded file for php version 72.
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