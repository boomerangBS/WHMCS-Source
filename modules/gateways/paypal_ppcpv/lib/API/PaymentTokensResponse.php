<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

namespace WHMCS\Module\Gateway\paypal_ppcpv\API;

// Decoded file for php version 72.
class PaymentTokensResponse extends AbstractResponse
{
    public $customer_id;
    public $payment_tokens;
    public function customerId()
    {
        return $this->customerId();
    }
    public function paymentTokens() : array
    {
        return $this->payment_tokens;
    }
    public function respond(HttpResponse $response) : AbstractResponse
    {
        return $this->assertHTTPOk($response)->withJSON($response->body);
    }
}

?>