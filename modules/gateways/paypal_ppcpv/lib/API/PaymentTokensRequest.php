<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

namespace WHMCS\Module\Gateway\paypal_ppcpv\API;

// Decoded file for php version 72.
class PaymentTokensRequest extends SimpleGetRequest
{
    use RequestAccessTokenAuthenticatedTrait;
    public $customerId = "";
    public function customerId($customerId) : \self
    {
        $this->customerId = $customerId;
        return $this;
    }
    public function send() : HttpResponse
    {
        return $this->partnerAttribution()->acceptJSON()->contentJSON()->get(sprintf("/v3/vault/payment-tokens?customer_id=%s", $this->customerId));
    }
    public function sendReady()
    {
        return 0 < strlen($this->customerId);
    }
    public function responseType() : AbstractResponse
    {
        return new PaymentTokensResponse();
    }
}

?>