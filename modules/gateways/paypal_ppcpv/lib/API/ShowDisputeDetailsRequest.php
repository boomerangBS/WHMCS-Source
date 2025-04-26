<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

namespace WHMCS\Module\Gateway\paypal_ppcpv\API;

// Decoded file for php version 72.
class ShowDisputeDetailsRequest extends SimpleGetRequest
{
    use RequestAccessTokenAuthenticatedTrait;
    protected $disputeId = "";
    public function send() : HttpResponse
    {
        return $this->acceptJSON()->get("/v1/customer/disputes/" . $this->disputeId);
    }
    public function responseType() : AbstractResponse
    {
        return new ShowDisputeDetailsResponse();
    }
    public function setIdentifier($disputeId) : \self
    {
        $this->disputeId = $disputeId;
        return $this;
    }
}

?>