<?php


namespace WHMCS\Module\Gateway\paypal_ppcpv\Handler\Event;
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