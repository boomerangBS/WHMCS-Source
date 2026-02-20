<?php


namespace WHMCS\Module\Gateway\paypal_ppcpv\API;
class PaymentTokensResponse extends AbstractResponse
{
    public $customer_id;
    public $payment_tokens;
    public function customerId()
    {
        return $this->customerId();
    }
    public function paymentTokens() : array
    {
        return $this->payment_tokens;
    }
    public function respond(HttpResponse $response) : AbstractResponse
    {
        return $this->assertHTTPOk($response)->withJSON($response->body);
    }
}

?>