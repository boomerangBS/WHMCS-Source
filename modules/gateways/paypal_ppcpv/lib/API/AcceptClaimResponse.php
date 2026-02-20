<?php


namespace WHMCS\Module\Gateway\paypal_ppcpv\API;
class AcceptClaimResponse extends AbstractResponse
{
    public $links;
    public function respond(HttpResponse $response) : AbstractResponse
    {
        return $this->assertHTTPOK($response)->withJSON($response->body);
    }
}

?>