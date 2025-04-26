<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

namespace WHMCS\Module\Gateway\paypal_ppcpv\API\Entity;

// Decoded file for php version 72.
abstract class AbstractPaymentSource
{
    protected $paymentType = "paypal";
    protected abstract function getDetails();
    public function get() : array
    {
        return [$this->paymentType => $this->getDetails()];
    }
    public static function factory(string $type)
    {
        $handlerClass = self::paymentSourceClass($type);
        $fqClass = "WHMCS\\Module\\Gateway\\paypal_ppcpv\\API\\Entity" . "\\" . $handlerClass;
        if(!class_exists($fqClass)) {
            throw new \RuntimeException("Class " . $fqClass . " not found");
        }
        return new $fqClass();
    }
    protected static function paymentSourceClass($type)
    {
        return sprintf("%sPaymentSource", ucfirst($type));
    }
}

?>