<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

namespace WHMCS\Module\Gateway\paypal_ppcpv\API;

// Decoded file for php version 72.
class PaymentCaptureLookupResponse extends PaymentLookupResponse
{
    public $final_capture = false;
    public $disbursement_mode = "";
    public $network_transaction_reference;
    public $seller_protection;
    public $seller_receivable_breakdown;
    public $processor_response;
    public $supplementary_data;
    public $payee;
    public function getFeesTotal()
    {
        $totalFee = 0;
        foreach ($this->seller_receivable_breakdown->platform_fees ?? [] as $fee) {
            $totalFee += $fee->amount->value ?? 0;
        }
        $totalFee += $this->seller_receivable_breakdown->paypal_fee->value ?? 0;
        return $totalFee;
    }
    public function getMerchantNetAmount()
    {
        $netAmount = $this->seller_receivable_breakdown->receivable_amount ?? NULL;
        if(is_null($netAmount)) {
            $netAmount = $this->seller_receivable_breakdown->net_amount ?? NULL;
        }
        if(is_object($netAmount)) {
            return $netAmount;
        }
        return (object) [];
    }
    public function hasOrderIdentifier()
    {
        return 0 < strlen($this->supplementary_data->related_ids->order_id ?? "");
    }
    public function getOrderIdentifier()
    {
        return $this->supplementary_data->related_ids->order_id ?? "";
    }
}

?>