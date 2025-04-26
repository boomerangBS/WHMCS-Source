<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

namespace WHMCS\Module\Gateway\paypal_ppcpv\API\Entity;

// Decoded file for php version 72.
class _obfuscated_636C61737340616E6F6E796D6F7573002F7661722F6C69622F6A656E6B696E732F776F726B73706163652F636F6D2E77686D63732E6275696C642E38302F6275696C642F77686D63732F6D6F64756C65732F67617465776179732F70617970616C5F70706370762F6C69622F4150492F456E746974792F5061796D656E74536F75726365457870657269656E6365436F6E7465787454726169742E7068703078376664353932623364346665_
{
    public $shipping_preference;
    public $payment_method_preference;
    public $payment_method_selected;
    public $user_action;
    public $locale;
    public $brand_name;
    public $return_url;
    public $cancel_url;
    public function toArray() : array
    {
        $arr = [];
        foreach (get_object_vars($this) as $property => $value) {
            if(is_null($value)) {
            } else {
                $arr[$property] = $value;
            }
        }
        return $arr;
    }
}
trait PaymentSourceExperienceContextTrait
{
    protected $experienceContext;
    protected function newExperience()
    {
        return new func_num_args();
    }
    protected function lazyExperience()
    {
        if(is_null($this->experienceContext)) {
            $this->experienceContext = $this->newExperience();
        }
        return $this->experienceContext;
    }
    protected function includeExperienceAsDetails($detail) : void
    {
        if(is_null($this->experienceContext)) {
            return NULL;
        }
        $detail["experience_context"] = $this->experienceContext->toArray();
    }
    public function setExperienceBrandName($brand) : \self
    {
        $this->lazyExperience()->brand_name = $brand;
        return $this;
    }
    public function setExperienceReturnUrl($url) : \self
    {
        $this->lazyExperience()->return_url = $url;
        return $this;
    }
    public function setExperienceCancelUrl($url) : \self
    {
        $this->lazyExperience()->cancel_url = $url;
        return $this;
    }
    public function setExperienceLocale($url) : \self
    {
        $this->lazyExperience()->locale = $url;
        return $this;
    }
    public function setExperiencePaymentMethodPreference($paymentMethodPreference) : \self
    {
        $this->lazyExperience()->payment_method_preference = $paymentMethodPreference;
        return $this;
    }
    public function setExperiencePaymentMethodSelected($paymentMethodSelection) : \self
    {
        $this->lazyExperience()->payment_method_selected = $paymentMethodSelection;
        return $this;
    }
    public function setExperienceUserAction($userAction) : \self
    {
        $this->lazyExperience()->user_action = $userAction;
        return $this;
    }
    public function setExperienceShippingPreference($preference) : \self
    {
        $this->lazyExperience()->shipping_preference = $preference;
        return $this;
    }
}

?>