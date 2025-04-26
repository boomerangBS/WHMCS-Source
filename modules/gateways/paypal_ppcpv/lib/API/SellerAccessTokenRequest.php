<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

namespace WHMCS\Module\Gateway\paypal_ppcpv\API;

// Decoded file for php version 72.
class SellerAccessTokenRequest extends AbstractRequest
{
    use RequestSendReadyAllPropertiesTrait;
    protected $authCode = "";
    protected $sellerNonce = "";
    protected $sharedIdentifier = "";
    public function setSellerNonce($sellerNonce) : \self
    {
        $this->sellerNonce = $sellerNonce;
        return $this;
    }
    public function setAuthCode($authCode) : \self
    {
        $this->authCode = $authCode;
        return $this;
    }
    public function setSharedIdentifier($sharedIdentifier) : \self
    {
        $this->sharedIdentifier = $sharedIdentifier;
        return $this;
    }
    public function send() : HttpResponse
    {
        return $this->partnerAttribution()->basicAuthentication($this->sharedIdentifier, "")->acceptJSON()->contentURLEncoded()->post("/v1/oauth2/token", $this->payload());
    }
    protected function payload()
    {
        return http_build_query(["grant_type" => "authorization_code", "code" => $this->authCode, "code_verifier" => $this->sellerNonce]);
    }
    public function responseType() : AbstractResponse
    {
        return new SellerAccessTokenResponse();
    }
}

?>