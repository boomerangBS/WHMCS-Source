<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

namespace WHMCS\Module\Gateway\paypal_ppcpv\API;

// Decoded file for php version 72.
class RetrieveSetupTokenRequest extends SimpleGetRequest
{
    use RequestAccessTokenAuthenticatedTrait;
    protected $id = "";
    public function send() : HttpResponse
    {
        return $this->partnerAttribution()->acceptJSON()->contentJSON()->get(sprintf("/v3/vault/setup-tokens/%s", $this->id));
    }
    public function sendReady()
    {
        return 0 < strlen($this->id);
    }
    public function responseType() : AbstractResponse
    {
        return new RetrieveSetupTokenResponse();
    }
    public function setSetupTokentIdentifier($setupTokenIdentifier) : \self
    {
        $this->id = $setupTokenIdentifier;
        return $this;
    }
}

?>