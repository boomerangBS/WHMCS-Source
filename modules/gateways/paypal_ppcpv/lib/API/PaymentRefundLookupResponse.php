<?php


namespace WHMCS\Module\Gateway\paypal_ppcpv\API;
class PaymentRefundLookupResponse extends PaymentLookupResponse
{
    public $acquirer_reference_number;
    public $seller_payable_breakdown;
    public $payer;
    public function getFeesTotal()
    {
        $totalFee = 0;
        foreach ($this->seller_payable_breakdown->platform_fees ?? [] as $fee) {
            $totalFee += $fee->amount->value ?? 0;
        }
        $totalFee += $this->seller_payable_breakdown->paypal_fee->value ?? 0;
        return $totalFee;
    }
    public function getMerchantNetAmount()
    {
        $netAmount = $this->seller_payable_breakdown->net_amount_breakdown[0]->payable_amount ?? NULL;
        if(is_null($netAmount)) {
            $netAmount = $this->seller_payable_breakdown->net_amount ?? NULL;
        }
        if(is_object($netAmount)) {
            return $netAmount;
        }
        return (object) [];
    }
    public function getCaptureTransactionIdentifier()
    {
        $captureLink = $this->link("up");
        if(is_null($captureLink)) {
            return "";
        }
        return basename($captureLink->href);
    }
}

?>