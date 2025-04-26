<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Module\Gateway\paypal_acdc\API\Entity;

class SetupTokenPaymentSource extends AbstractPaymentSource
{
    protected $paymentType = "token";
    protected $identifier;
    protected $type = "SETUP_TOKEN";
    protected function getDetails() : array
    {
        if(empty($this->identifier)) {
            throw RuntimeException("Token identifier required");
        }
        $details = parent::getDetails();
        $details["id"] = $this->identifier;
        $details["type"] = $this->type;
        return $details;
    }
    public function setIdentifier($identifier) : \self
    {
        $this->identifier = $identifier;
        return $this;
    }
}

?>