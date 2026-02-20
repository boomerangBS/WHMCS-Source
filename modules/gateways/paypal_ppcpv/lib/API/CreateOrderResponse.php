<?php


namespace WHMCS\Module\Gateway\paypal_ppcpv\API;
class CreateOrderResponse extends AbstractResponse implements OrderResponseInterface
{
    use OrderResponseTrait;
    public function respond(HttpResponse $response) : AbstractResponse
    {
        return $this->assertHTTPSuccess($response)->withJSON($response->body);
    }
}

?>