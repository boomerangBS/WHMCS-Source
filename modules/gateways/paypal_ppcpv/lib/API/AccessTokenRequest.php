<?php


namespace WHMCS\Module\Gateway\paypal_ppcpv\API;
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