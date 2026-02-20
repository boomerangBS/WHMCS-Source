<?php


namespace WHMCS\Module\Gateway\paypal_ppcpv\API;
class CreatePaymentTokenResponse extends AbstractResponse
{
    public $payment_source;
    public $links;
    public $id;
    public $customer;
    public function respond(HttpResponse $response) : AbstractResponse
    {
        return $this->assertHTTPSuccess($response)->withJSON($response->body);
    }
    public function getVaultTokenIdentifier()
    {
        return $this->id;
    }
    public function getCustomerIdentifier()
    {
        return $this->customer->id;
    }
    public function getPaymentSource()
    {
        return $this->payment_source;
    }
    public function pack()
    {
        $vars = get_object_vars($this);
        unset($vars["id"]);
        foreach ($vars["links"] as $link) {
            unset($link->href);
        }
        return json_encode($vars);
    }
}

?>