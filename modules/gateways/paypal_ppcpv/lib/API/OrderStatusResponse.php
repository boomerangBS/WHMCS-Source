<?php


namespace WHMCS\Module\Gateway\paypal_ppcpv\API;
class OrderStatusResponse extends AbstractResponse implements OrderResponseInterface
{
    use OrderResponseTrait;
    public function respond(HttpResponse $response) : AbstractResponse
    {
        return $this->assertHTTPOk($response)->withJSON($response->body);
    }
    public function referenceId()
    {
        return $this->purchase_units[0]->reference_id ?? "";
    }
    public function invoiceIdentifier()
    {
        if(isset($this->purchase_units[0]->invoice_id)) {
            return $this->purchase_units[0]->invoice_id;
        }
        return NULL;
    }
    public function liabilityShift()
    {
        return $this->payment_source->card->authentication_result->liability_shift ?? NULL;
    }
    public function isLiabilityShifted()
    {
        $liabilityShift = $this->liabilityShift();
        return is_null($liabilityShift) || in_array($liabilityShift, ["POSSIBLE", "YES"]);
    }
    public function threeDSecureResponse() : Entity\CardAuthenticationResult
    {
        return Entity\CardAuthenticationResult::factory($this->payment_source->card->authentication_result ?? NULL);
    }
    public function getPayer()
    {
        if(!isset($this->payer)) {
            return NULL;
        }
        return (new Entity\PaypalPaymentSourceResponse())->makePayer($this->payer);
    }
}

?>