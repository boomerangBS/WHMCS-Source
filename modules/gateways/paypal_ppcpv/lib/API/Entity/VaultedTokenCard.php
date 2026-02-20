<?php


namespace WHMCS\Module\Gateway\paypal_ppcpv\API\Entity;
class VaultedTokenCard extends VaultedToken
{
    protected $cardHint = "";
    protected $brand = "";
    protected $cardExpiry = "";
    public static function factory(string $customerId, string $vaultId, string $transactionIdentifier, $paymentSource)
    {
        if(!isset($paymentSource->card)) {
            return NULL;
        }
        $self = new self();
        $self->customerId = $customerId;
        $self->vaultId = $vaultId;
        $self->transactionIdentifier = $transactionIdentifier ?? "";
        $self->cardHint = $paymentSource->card->last_digits;
        $self->brand = $paymentSource->card->brand;
        if(isset($paymentSource->card->expiry)) {
            $self->cardExpiry = $paymentSource->card->expiry;
        }
        return $self;
    }
    public function cardHint()
    {
        return $this->cardHint;
    }
    public function cardExpiry()
    {
        return $this->cardExpiry;
    }
    public function cardExpiryCarbon()
    {
        return \WHMCS\Carbon::createFromFormat("Y-m", $this->cardExpiry());
    }
    public function cardBrand()
    {
        return $this->brand;
    }
}

?>