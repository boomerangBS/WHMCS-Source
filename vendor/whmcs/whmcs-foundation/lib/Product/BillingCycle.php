<?php

namespace WHMCS\Product;

class BillingCycle
{
    protected $pricingData;
    protected $billingCycles = ["Monthly" => ["price" => "monthly", "setup_fee" => "msetupfee"], "Quarterly" => ["price" => "quarterly", "setup_fee" => "qsetupfee"], "Semi-Annually" => ["price" => "semiannually", "setup_fee" => "ssetupfee"], "Annually" => ["price" => "annually", "setup_fee" => "asetupfee"], "Biennially" => ["price" => "biennially", "setup_fee" => "bsetupfee"], "Triennially" => ["price" => "triennially", "setup_fee" => "tsetupfee"]];
    public function __construct($pricingData)
    {
        $this->pricingData = $pricingData;
    }
    public function getBillingCycles() : array
    {
        $result = [];
        foreach ($this->billingCycles as $cycle => $keys) {
            $price = $this->pricingData->{$keys["price"]} ?? 0;
            $setupFee = $this->pricingData->{$keys["setup_fee"]} ?? 0;
            if(0 < $price) {
                $result[$cycle] = ["price" => $price, "setup_fee" => $setupFee];
            }
        }
        return $result;
    }
}

?>