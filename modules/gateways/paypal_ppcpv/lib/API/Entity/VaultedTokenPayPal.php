<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

namespace WHMCS\Module\Gateway\paypal_ppcpv\API\Entity;

// Decoded file for php version 72.
class VaultedTokenPayPal extends VaultedToken
{
    protected $brand = "PayPal";
    protected $payPalEmail = "";
    public static function factory(string $customerId, string $vaultId, string $transactionIdentifier, $paymentSource)
    {
        if(!isset($paymentSource->paypal)) {
            return NULL;
        }
        $self = new self();
        $self->customerId = $customerId;
        $self->vaultId = $vaultId;
        $self->transactionIdentifier = $transactionIdentifier ?? "";
        $self->payPalEmail = $paymentSource->paypal->email_address;
        return $self;
    }
    public function payPalEmail()
    {
        return $this->payPalEmail;
    }
    public function cardExpiry() : \WHMCS\Carbon
    {
        return \WHMCS\Carbon::today()->addYears(30);
    }
}

?>