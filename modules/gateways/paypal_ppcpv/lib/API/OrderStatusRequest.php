<?php


namespace WHMCS\Module\Gateway\paypal_ppcpv\API;
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