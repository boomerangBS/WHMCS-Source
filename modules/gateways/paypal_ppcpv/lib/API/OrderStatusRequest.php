<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

namespace WHMCS\Module\Gateway\paypal_ppcpv\API;

// Decoded file for php version 72.
class OrderStatusRequest extends SimpleGetRequest
{
    use RequestAccessTokenAuthenticatedTrait;
    protected $orderId = "";
    public function send() : HttpResponse
    {
        return $this->partnerAttribution()->acceptJSON()->contentJSON()->get(sprintf("/v2/checkout/orders/%s", $this->orderId));
    }
    public function sendReady()
    {
        return 0 < strlen($this->orderId);
    }
    public function responseType() : AbstractResponse
    {
        return new OrderStatusResponse();
    }
    public function setOrderIdentifier($orderIdentifier) : \self
    {
        $this->orderId = $orderIdentifier;
        return $this;
    }
}

?>