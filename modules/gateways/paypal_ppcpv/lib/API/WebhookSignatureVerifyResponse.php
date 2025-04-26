<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

namespace WHMCS\Module\Gateway\paypal_ppcpv\API;

// Decoded file for php version 72.
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