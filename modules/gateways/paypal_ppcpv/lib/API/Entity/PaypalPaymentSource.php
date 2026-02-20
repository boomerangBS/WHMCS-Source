<?php


namespace WHMCS\Module\Gateway\paypal_ppcpv\API\Entity;
class PaypalPaymentSource extends AbstractPaymentSource
{
    use PaymentSourceExperienceContextTrait;
    protected $attributes = [];
    protected $usageType = "";
    protected $customerType = "";
    protected $permitMultiplePaymentTokens;
    public function __construct()
    {
        $this->lazyExperience()->shipping_preference = "NO_SHIPPING";
    }
    protected function getDetails() : array
    {
        $detail = [];
        $this->includeExperienceAsDetails($detail);
        if(!empty($this->attributes)) {
            $detail["attributes"] = $this->attributes;
        }
        if($this->usageType !== "") {
            $detail["usage_type"] = $this->usageType;
        }
        if($this->customerType !== "") {
            $detail["customer_type"] = $this->customerType;
        }
        if(!is_null($this->permitMultiplePaymentTokens)) {
            $detail["permit_multiple_payment_tokens"] = $this->permitMultiplePaymentTokens;
        }
        return $detail;
    }
    public function setUsageType($usageType) : \self
    {
        $this->usageType = $usageType;
        return $this;
    }
    public function setCustomerType($customerType) : \self
    {
        $this->customerType = $customerType;
        return $this;
    }
    public function setPermitMultiplePaymentTokens($permit) : \self
    {
        $this->permitMultiplePaymentTokens = $permit;
        return $this;
    }
    public function enableVaulting() : \self
    {
        $this->attributes["vault"] = ["permit_multiple_payment_tokens" => true, "store_in_vault" => "ON_SUCCESS", "usage_type" => "MERCHANT", "customer_type" => "CONSUMER"];
        return $this;
    }
}

?>