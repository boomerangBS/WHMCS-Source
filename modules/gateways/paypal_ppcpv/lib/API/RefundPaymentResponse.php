<?php


namespace WHMCS\Module\Gateway\paypal_ppcpv\API;
class RefundPaymentResponse extends RefundDetailsResponse
{
    public function respond(HttpResponse $response) : AbstractResponse
    {
        return $this->assertHTTPSuccess($response)->withJSON($response->body);
    }
    public function getRefundIdentifier()
    {
        return $this->id;
    }
}

?>