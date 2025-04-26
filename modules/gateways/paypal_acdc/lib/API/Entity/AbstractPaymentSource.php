<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Module\Gateway\paypal_acdc\API\Entity;

abstract class AbstractPaymentSource extends \WHMCS\Module\Gateway\paypal_ppcpv\API\Entity\AbstractPaymentSource
{
    protected $paymentType = "card";
    protected $storedCredential = [];
    const CUSTOMER_FIRST = "customerFirstPayment";
    const CUSTOMER_SUBSEQUENT = "customerSubsequentPayment";
    const MERCHANT_UNSCHEDULED = "merchantUnscheduledPayment";
    const MERCHANT_RECURRING = "merchantRecurringPayment";
    public static function factory(string $type)
    {
        $handlerClass = self::paymentSourceClass($type);
        $fqClass = "WHMCS\\Module\\Gateway\\paypal_acdc\\API\\Entity" . "\\" . $handlerClass;
        if(!class_exists($fqClass)) {
            throw new \RuntimeException("Class " . $fqClass . " not found");
        }
        return new $fqClass();
    }
    protected function getDetails() : array
    {
        $details = [];
        if(!empty($this->storedCredential)) {
            $details["stored_credential"] = $this->storedCredential;
        }
        return $details;
    }
    public function setStoredCredentialByType($credentialType) : \self
    {
        switch ($credentialType) {
            case self::CUSTOMER_FIRST:
                $storedCredentialValues = ["payment_initiator" => "CUSTOMER", "payment_type" => "RECURRING", "usage" => "FIRST"];
                break;
            case self::CUSTOMER_SUBSEQUENT:
                $storedCredentialValues = ["payment_initiator" => "CUSTOMER", "payment_type" => "RECURRING", "usage" => "SUBSEQUENT"];
                break;
            case self::MERCHANT_UNSCHEDULED:
                $storedCredentialValues = ["payment_initiator" => "MERCHANT", "payment_type" => "UNSCHEDULED", "usage" => "SUBSEQUENT"];
                break;
            case self::MERCHANT_RECURRING:
                $storedCredentialValues = ["payment_initiator" => "MERCHANT", "payment_type" => "RECURRING", "usage" => "SUBSEQUENT"];
                $this->storedCredential = $storedCredentialValues;
                return $this;
                break;
            default:
                throw new \RuntimeException($credentialType . " not valid");
        }
    }
}

?>