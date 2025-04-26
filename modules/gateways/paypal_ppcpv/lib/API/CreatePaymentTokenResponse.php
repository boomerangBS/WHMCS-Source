<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

namespace WHMCS\Module\Gateway\paypal_ppcpv\API;

// Decoded file for php version 72.
class CreatePaymentTokenResponse extends AbstractResponse
{
    public $payment_source;
    public $links;
    public $id;
    public $customer;
    public function respond(HttpResponse $response) : AbstractResponse
    {
        return $this->assertHTTPSuccess($response)->withJSON($response->body);
    }
    public function getVaultTokenIdentifier()
    {
        return $this->id;
    }
    public function getCustomerIdentifier()
    {
        return $this->customer->id;
    }
    public function getPaymentSource()
    {
        return $this->payment_source;
    }
    public function pack()
    {
        $vars = get_object_vars($this);
        unset($vars["id"]);
        foreach ($vars["links"] as $link) {
            unset($link->href);
        }
        return json_encode($vars);
    }
}

?>