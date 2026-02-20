<?php


namespace WHMCS\Module\Gateway\paypal_ppcpv\API;
class MerchantStatusResponse extends AbstractResponse
{
    public $merchant_id;
    public $products;
    public $capabilities;
    public $payments_receivable;
    public $legal_name;
    public $primary_email_confirmed;
    public function paymentsReceivable()
    {
        return (bool) $this->payments_receivable;
    }
    public function primaryEmailConfirmed()
    {
        return (bool) $this->primary_email_confirmed;
    }
    public function productSubscribed($product)
    {
        return $this->productStatus($product) == "SUBSCRIBED";
    }
    public function productStatus($product)
    {
        $struct = $this->product($product);
        if(is_null($struct)) {
            return "";
        }
        if(property_exists($struct, "vetting_status")) {
            return $struct->vetting_status;
        }
        return $struct->status ?? "";
    }
    public function product($product)
    {
        foreach ($this->products as $struct) {
            if(isset($struct->name) && $struct->name == $product) {
                return $struct;
            }
        }
        return NULL;
    }
    public function capabilityActive($capability)
    {
        return $this->capability($capability) == "ACTIVE";
    }
    public function capability($capability)
    {
        foreach ($this->capabilities as $struct) {
            if(isset($struct->name) && $struct->name == $capability) {
                return $struct->status ?? "";
            }
        }
        return NULL;
    }
    public function respond(HttpResponse $response) : AbstractResponse
    {
        return $this->assertHTTPOk($response)->withJSON($response->body);
    }
}

?>