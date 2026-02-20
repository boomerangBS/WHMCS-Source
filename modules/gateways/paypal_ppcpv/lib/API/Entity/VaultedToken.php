<?php


namespace WHMCS\Module\Gateway\paypal_ppcpv\API\Entity;
class VaultedToken
{
    protected $customerId;
    protected $vaultId;
    protected $transactionIdentifier = "";
    public static function factoryByPaymentSource($paymentSource) : \self
    {
        $self = new self();
        $paySource = current(get_object_vars($paymentSource));
        if(!isset($paySource->attributes->vault->status) || $paySource->attributes->vault->status !== "VAULTED") {
            return NULL;
        }
        $self->vaultId = $paySource->attributes->vault->id ?? "";
        $self->customerId = $paySource->attributes->vault->customer->id ?? "";
        return $self;
    }
    public function transformToTokenJSON()
    {
        if(!$this->vaultId()) {
            return "";
        }
        return json_encode(["vaultId" => $this->vaultId(), "customerId" => $this->customerId(), "transactionIdentifier" => $this->transactionIdentifier()]);
    }
    public function customerId()
    {
        return $this->customerId;
    }
    public function vaultId()
    {
        return $this->vaultId;
    }
    public function transactionIdentifier()
    {
        return $this->transactionIdentifier;
    }
    public function brand()
    {
        return $this->brand;
    }
    public static function factory(string $customerId, string $vaultId, string $transactionIdentifier, $paymentSource)
    {
        if(strlen($vaultId) == 0) {
            return NULL;
        }
        if(isset($paymentSource->card)) {
            return VaultedTokenCard::factory($customerId, $vaultId, $transactionIdentifier, $paymentSource);
        }
        return VaultedTokenPayPal::factory($customerId, $vaultId, $transactionIdentifier, $paymentSource);
    }
    public function equals(VaultedToken $token) : VaultedToken
    {
        return $this->vaultId() === $token->vaultId() && $this->customerId() === $token->customerId();
    }
}

?>