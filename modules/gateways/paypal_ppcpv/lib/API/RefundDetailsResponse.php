<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

namespace WHMCS\Module\Gateway\paypal_ppcpv\API;

// Decoded file for php version 72.
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