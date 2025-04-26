<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

namespace WHMCS\Module\Gateway\paypal_ppcpv\API;

// Decoded file for php version 72.
class SellerCredentialsRequest extends SimpleGetRequest
{
    use RequestSendReadyAllPropertiesTrait;
    protected $token = "";
    public function send() : HttpResponse
    {
        return $this->partnerAttribution()->acceptJSON()->contentURLEncoded()->bearerAuthorization($this->token)->get(sprintf("/v1/customer/partners/%s/merchant-integrations/credentials", $this->env()->partnerId));
    }
    public function responseType() : AbstractResponse
    {
        return new SellerCredentialsResponse();
    }
    public function setToken($token) : \self
    {
        $this->token = $token;
        return $this;
    }
}

?>