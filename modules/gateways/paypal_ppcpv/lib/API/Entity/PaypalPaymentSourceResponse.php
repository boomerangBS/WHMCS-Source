<?php


namespace WHMCS\Module\Gateway\paypal_ppcpv\API\Entity;
class PaypalPaymentSourceResponse extends PaymentSourceResponse
{
    public $email_address = "";
    public $account_id = "";
    public $account_status = "";
    public $name;
    public $address;
    public $attributes;
    public static function factory($responsePaymentSource)
    {
        return \WHMCS\Module\Gateway\paypal_ppcpv\Util::overlayMapOnObject($responsePaymentSource, new self());
    }
    public function getType()
    {
        return "paypal";
    }
    public function getPayer()
    {
        return $this->makePayer($this);
    }
    public function makePayer($payer)
    {
        $payerTyped = new func_num_args();
        if(is_object($payer->name)) {
            $payerTyped->firstname = $payer->name->given_name ?? "";
            $payerTyped->lastName = $payer->name->surname ?? "";
        }
        $payerTyped->emailAddress = $payer->email_address ?? "";
        return $payerTyped;
    }
}
class _obfuscated_5C636C61737340616E6F6E796D6F7573002F7661722F6C69622F6A656E6B696E732F776F726B73706163652F636F6D2E77686D63732E6275696C642E38302F6275696C642F77686D63732F6D6F64756C65732F67617465776179732F70617970616C5F70706370762F6C69622F4150492F456E746974792F50617970616C5061796D656E74536F75726365526573706F6E73652E7068703078376664353934323461653535_
{
    public $firstname = "";
    public $lastName = "";
    public $emailAddress = "";
    public function fullName()
    {
        return trim($this->firstname . " " . $this->lastName);
    }
}

?>