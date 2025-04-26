<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

namespace WHMCS\Module\Gateway\paypal_ppcpv\API;

// Decoded file for php version 72.
class RefundDetailsRequest extends SimpleGetRequest
{
    use RequestAccessTokenAuthenticatedTrait;
    protected $refundIdentifier = "";
    public function send() : HttpResponse
    {
        return $this->acceptJSON()->get("/v2/payments/refunds/" . $this->refundIdentifier);
    }
    public function responseType() : AbstractResponse
    {
        return new RefundDetailsResponse();
    }
    public function setRefundIdentifier($refundIdentifier) : \self
    {
        $this->refundIdentifier = $refundIdentifier;
        return $this;
    }
}

?>