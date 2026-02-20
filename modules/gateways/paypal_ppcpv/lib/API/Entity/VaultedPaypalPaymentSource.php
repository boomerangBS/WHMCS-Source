<?php


namespace WHMCS\Module\Gateway\paypal_ppcpv\API\Entity;
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