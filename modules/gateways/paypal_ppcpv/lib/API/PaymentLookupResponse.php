<?php


namespace WHMCS\Module\Gateway\paypal_ppcpv\API;
abstract class PaymentLookupResponse extends AbstractResponse
{
    public $status = "";
    public $status_details;
    public $id = "";
    public $invoice_id = "";
    public $custom_id = "";
    public $links = "";
    public $amount;
    public $create_time = "";
    public $update_time = "";
    public $note_to_payer = "";
    public function respond(HttpResponse $response) : AbstractResponse
    {
        return $this->assertHTTPSuccess($response)->withJSON($response->body);
    }
    public abstract function getMerchantNetAmount();
    public function getStatusReason()
    {
        if(!is_object($this->status_details)) {
            return NULL;
        }
        return $this->status_details->reason ?? "";
    }
    public function link(string $relation)
    {
        if(!is_null($this->links)) {
            foreach ($this->links as $link) {
                if($link->rel === $relation) {
                    return $link;
                }
            }
        }
        return NULL;
    }
}

?>