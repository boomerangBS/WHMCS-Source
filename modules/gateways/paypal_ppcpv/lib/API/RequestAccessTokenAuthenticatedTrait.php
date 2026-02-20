<?php


namespace WHMCS\Module\Gateway\paypal_ppcpv\API;
trait RequestAccessTokenAuthenticatedTrait
{
    public function accessToken(string $token)
    {
        $this->bearerAuthorization($token);
        return $this;
    }
}

?>