<?php


namespace WHMCS\Module\Gateway\paypal_ppcpv\API\Entity;
abstract class PaymentSourceResponse
{
    protected $responsePaymentSource;
    public static function factory($responsePaymentSource)
    {
        $type = key(get_object_vars($responsePaymentSource));
        $paymentSource = $responsePaymentSource->{$type};
        if($type == "card") {
            return CardPaymentSourceResponse::factory($paymentSource);
        }
        if($type == "paypal") {
            return PaypalPaymentSourceResponse::factory($paymentSource);
        }
        throw new \InvalidArgumentException("Unknown payment source type '" . $type . "'");
    }
    public abstract function getType();
}

?>