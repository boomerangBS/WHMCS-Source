<?php


namespace WHMCS\Module\Gateway\paypal_ppcpv\Handler\Event;
trait VaultTokenControllerRequired
{
    protected $vaultTokenController;
    public function setVaultTokenController($vaultTokenController) : \self
    {
        $this->vaultTokenController = $vaultTokenController;
        return $this;
    }
}

?>