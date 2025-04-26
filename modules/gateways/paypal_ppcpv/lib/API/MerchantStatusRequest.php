<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

namespace WHMCS\Module\Gateway\paypal_ppcpv\API;

// Decoded file for php version 72.
class MerchantStatusRequest extends SimpleGetRequest
{
    use RequestAccessTokenAuthenticatedTrait;
    public function send() : HttpResponse
    {
        $env = $this->env();
        return $this->partnerAttribution()->acceptJSON()->contentJSON()->get(sprintf("/v1/customer/partners/%s/merchant-integrations/%s", $env->partnerId, $env->payerId));
    }
    public function sendReady()
    {
        $env = $this->env();
        return 0 < strlen($env->partnerId) && 0 < strlen($env->payerId);
    }
    public function responseType() : AbstractResponse
    {
        return new MerchantStatusResponse();
    }
}

?>