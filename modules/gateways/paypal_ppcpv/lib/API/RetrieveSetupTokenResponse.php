<?php


namespace WHMCS\Module\Gateway\paypal_ppcpv\API;
class RetrieveSetupTokenResponse extends AbstractResponse
{
    public $id = "";
    public $customer = "";
    public $status = "";
    public $payment_source = [];
    public $links = [];
    public function respond(HttpResponse $response) : AbstractResponse
    {
        return $this->assertHTTPStatus($response, 200)->withJSON($response->body);
    }
    public function paymentSource()
    {
        return $this->payment_source;
    }
}

?>