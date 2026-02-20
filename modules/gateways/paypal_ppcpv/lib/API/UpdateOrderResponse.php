<?php


namespace WHMCS\Module\Gateway\paypal_ppcpv\API;
class UpdateOrderResponse extends AbstractResponse
{
    public function respond(HttpResponse $response) : AbstractResponse
    {
        return $this->assertHTTPStatus($response, 204);
    }
}

?>