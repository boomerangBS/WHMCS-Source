<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

namespace WHMCS\Module\Gateway\paypal_ppcpv\Handler\Event;

// Decoded file for php version 72.
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