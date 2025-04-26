<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

namespace WHMCS\Module\Gateway\paypal_ppcpv\API\Entity;

// Decoded file for php version 72.
class CardPaymentSourceResponse extends PaymentSourceResponse
{
    public $name = "";
    public $last_digits = "";
    public $expiry = "";
    public $brand = "";
    public $type = "";
    public $available_networks = [];
    public $attributes;
    public $bin_details;
    public static function factory($responsePaymentSource)
    {
        return \WHMCS\Module\Gateway\paypal_ppcpv\Util::overlayMapOnObject($responsePaymentSource, new self());
    }
    public function getType()
    {
        return "card";
    }
    public function name()
    {
        return $this->name ?? "";
    }
    public function brand()
    {
        return $this->brand ?? "";
    }
    public function hint()
    {
        return $this->last_digits ?? "";
    }
    public function expiry()
    {
        return $this->expiry ?? "";
    }
    public function networkType()
    {
        return $this->type ?? "";
    }
}

?>