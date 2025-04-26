<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

namespace WHMCS\Module\Gateway\paypal_ppcpv\API;

// Decoded file for php version 72.
class CreateSetupTokenResponse extends AbstractResponse
{
    public $payment_source;
    public $links;
    public $id;
    public $ordinal;
    public $customer;
    public $status;
    public function respond(HttpResponse $response) : AbstractResponse
    {
        return $this->assertHTTPStatus($response, 201)->withJSON($response->body);
    }
    public function getIdentifier()
    {
        return $this->id;
    }
}

?>