<?php


namespace WHMCS\Module\Gateway\paypal_ppcpv\API;
class RefundDetailsResponse extends AbstractResponse
{
    public $status = "";
    public $status_details;
    public $id = "";
    public $invoice_id = "";
    public $custom_id = "";
    public $acquirer_reference_number = "";
    public $note_to_payer = "";
    public $seller_payable_breakdown;
    public $links = [];
    public $amount;
    public $payer;
    public $create_time = "";
    public $update_time = "";
    public function respond(HttpResponse $response) : AbstractResponse
    {
        return $this->assertHTTPOK($response)->withJSON($response->body);
    }
    public function getPayPalFeeValue()
    {
        return $this->seller_payable_breakdown->paypal_fee->value ?? "";
    }
}

?>