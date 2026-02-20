<?php


namespace WHMCS\Module\Gateway\paypal_ppcpv\API;
class SellerAccessTokenResponse extends AbstractResponse
{
    public $scope = "";
    public $access_token = "";
    public $refresh_token = "";
    public $token_type = "";
    public $app_id = "";
    public $expires_in = -1;
    public $nonce = "";
    public function token()
    {
        return $this->access_token;
    }
    public function refreshToken()
    {
        return $this->refresh_token;
    }
    public function respond(HttpResponse $response) : AbstractResponse
    {
        return $this->assertHTTPOK($response)->withJSON($response->body);
    }
}

?>