<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Module\Gateway\paypal_acdc\API\Entity;

class VaultedCardPaymentSource extends AbstractPaymentSource
{
    protected $vaultId = "";
    protected $attributes = [];
    protected $transactionIdentifier = "";
    protected function getDetails() : array
    {
        $details = parent::getDetails();
        $details["vault_id"] = $this->vaultId;
        if(0 < count($this->attributes)) {
            $details["attributes"] = $this->attributes;
        }
        if(0 < strlen($this->transactionIdentifier) && isset($details["stored_credential"]) && $details["stored_credential"]["payment_initiator"] == "MERCHANT") {
            $details["stored_credential"]["previous_transaction_reference"] = $this->transactionIdentifier;
        }
        return $details;
    }
    public function setVaultId($vaultId) : \self
    {
        $this->vaultId = $vaultId;
        return $this;
    }
    public function setTransactionIdentifier($reference) : \self
    {
        $this->transactionIdentifier = $reference;
        return $this;
    }
    public function enable3DS() : \self
    {
        $this->attributes["verification"]["method"] = "SCA_WHEN_REQUIRED";
        return $this;
    }
}

?>