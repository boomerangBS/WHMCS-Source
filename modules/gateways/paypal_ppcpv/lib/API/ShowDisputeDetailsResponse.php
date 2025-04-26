<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

namespace WHMCS\Module\Gateway\paypal_ppcpv\API;

// Decoded file for php version 72.
class ShowDisputeDetailsResponse extends AbstractResponse
{
    public $dispute_id;
    public $disputed_transactions;
    public $external_reason_code;
    public $adjudications;
    public $money_movements;
    public $messages;
    public $evidences;
    public $supporting_info;
    public $links;
    public $create_time;
    public $update_time;
    public $reason;
    public $status;
    public $dispute_amount;
    public $dispute_asset;
    public $fee_policy;
    public $dispute_outcome;
    public $dispute_life_cycle_stage;
    public $dispute_channel;
    public $extensions;
    public $buyer_response_due_date;
    public $seller_response_due_date;
    public $offer;
    public $refund_details;
    public $communication_details;
    public $allowed_response_options;
    public function respond(HttpResponse $response) : AbstractResponse
    {
        return $this->assertHTTPOK($response)->withJSON($response->body);
    }
    public function getTransactionId()
    {
        return $this->disputed_transactions[0]->seller_transaction_id;
    }
}

?>