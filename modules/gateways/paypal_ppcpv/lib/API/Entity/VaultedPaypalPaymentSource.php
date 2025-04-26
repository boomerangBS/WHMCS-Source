<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

namespace WHMCS\Module\Gateway\paypal_ppcpv\API\Entity;

// Decoded file for php version 72.
class VaultedPaypalPaymentSource extends PaypalPaymentSource
{
    protected $vaultId = "";
    protected function getDetails() : array
    {
        $details = [];
        if(substr($this->vaultId, 0, 2) == "B-") {
            $details["billing_agreement_id"] = $this->vaultId;
        } else {
            $details["vault_id"] = $this->vaultId;
        }
        return $details;
    }
    public function setVaultId($vaultId) : \self
    {
        $this->vaultId = $vaultId;
        return $this;
    }
    public function withVaultedToken(VaultedToken $token) : \self
    {
        return $this->setVaultId($token->vaultId());
    }
}

?>