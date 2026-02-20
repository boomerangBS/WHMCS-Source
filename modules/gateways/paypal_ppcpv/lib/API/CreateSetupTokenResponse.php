<?php


namespace WHMCS\Module\Gateway\paypal_ppcpv\API;
class CreateSetupTokenResponse extends AbstractResponse
{
    public $payment_source;
    public $links;
    public $id;
    public $ordinal;
    public $customer;
    public $status;
    public function respond(HttpResponse $response) : AbstractResponse
    {
        return $this->assertHTTPStatus($response, 201)->withJSON($response->body);
    }
    public function getIdentifier()
    {
        return $this->id;
    }
}

?>