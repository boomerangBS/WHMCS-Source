<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

namespace WHMCS\Module\Gateway\paypal_ppcpv\Handler\Event;

// Decoded file for php version 72.
class VaultPaymentTokenDeleted extends AbstractWebhookHandler
{
    public function handle(\WHMCS\Module\Gateway\paypal_ppcpv\API\AbstractWebhookEvent $event, &$outcomes) : \WHMCS\Module\Gateway\paypal_ppcpv\API\AbstractWebhookEvent
    {
        $vaultedId = $event->vaultedId();
        if(empty($vaultedId)) {
            throw new \Exception("Vaulted Id is empty");
        }
        $this->vaultTokenControllerPPCPV()->deleteVaultedToken($vaultedId);
        $this->vaultTokenControllerACDC()->deleteVaultedToken($vaultedId);
        return "Vault Payment Token Deleted";
    }
    private function vaultTokenControllerPPCPV() : \WHMCS\Module\Gateway\paypal_ppcpv\VaultTokenController
    {
        return \WHMCS\Module\Gateway\paypal_ppcpv\VaultTokenController::factory(\WHMCS\Module\Gateway\paypal_ppcpv\PayPalCommerce::loadModule());
    }
    private function vaultTokenControllerACDC() : \WHMCS\Module\Gateway\paypal_acdc\VaultTokenController
    {
        return \WHMCS\Module\Gateway\paypal_acdc\VaultTokenController::factory(\WHMCS\Module\Gateway\paypal_acdc\Core::loadModule());
    }
}

?>