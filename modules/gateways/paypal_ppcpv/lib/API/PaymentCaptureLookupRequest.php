<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

namespace WHMCS\Module\Gateway\paypal_ppcpv\API;

// Decoded file for php version 72.
class PaymentCaptureLookupRequest extends SimpleGetRequest
{
    use RequestAccessTokenAuthenticatedTrait;
    protected $transactionIdentifier = "";
    public function send() : HttpResponse
    {
        return $this->partnerAttribution()->acceptJSON()->contentJSON()->get(sprintf("/v2/payments/captures/%s", $this->transactionIdentifier));
    }
    public function sendReady()
    {
        return 0 < strlen($this->transactionIdentifier);
    }
    public function responseType() : AbstractResponse
    {
        return new PaymentCaptureLookupResponse();
    }
    public function setTransactionIdentifier($transactionIdentifier) : \self
    {
        $this->transactionIdentifier = $transactionIdentifier;
        return $this;
    }
}

?>