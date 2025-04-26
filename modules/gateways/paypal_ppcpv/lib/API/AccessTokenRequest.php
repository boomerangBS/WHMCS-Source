<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

namespace WHMCS\Module\Gateway\paypal_ppcpv\API;

// Decoded file for php version 72.
class AccessTokenRequest extends AbstractRequest
{
    public function sendReady()
    {
        $env = $this->env();
        return !(strlen($env->clientId) == 0 || strlen($env->clientSecret) == 0);
    }
    public function send() : HttpResponse
    {
        $env = $this->env();
        return $this->basicAuthentication($env->clientId, $env->clientSecret)->acceptJSON()->contentURLEncoded()->post("/v1/oauth2/token", $this->payload());
    }
    protected function payload()
    {
        return http_build_query(["grant_type" => "client_credentials"]);
    }
    public function responseType() : AbstractResponse
    {
        return new AccessTokenResponse();
    }
}

?>