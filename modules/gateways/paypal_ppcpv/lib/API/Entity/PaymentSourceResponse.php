<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

namespace WHMCS\Module\Gateway\paypal_ppcpv\API\Entity;

// Decoded file for php version 72.
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