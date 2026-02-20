<?php

namespace WHMCS\Module\Gateway\paypal_ppcpv\Handler;

class PaymentToken extends AbstractHandler
{
    public function delete(\WHMCS\Module\Gateway\paypal_ppcpv\API\Entity\VaultedToken $vaultedToken) : array
    {
        if(!$vaultedToken->vaultId()) {
            return NULL;
        }
        try {
            $this->api()->send((new \WHMCS\Module\Gateway\paypal_ppcpv\API\DeletePaymentTokenRequest($this->api()))->setVaultId($vaultedToken->vaultId()));
            return $this->success($vaultedToken);
        } catch (\Exception $e) {
            return $this->error($vaultedToken);
        }
    }
    private function success(\WHMCS\Module\Gateway\paypal_ppcpv\API\Entity\VaultedToken $vaultedToken) : array
    {
        return ["status" => "success", "rawdata" => ["action" => "PaymentToken Delete", "token" => $vaultedToken->transformToTokenJSON()]];
    }
    private function error(\WHMCS\Module\Gateway\paypal_ppcpv\API\Entity\VaultedToken $vaultedToken) : array
    {
        return ["status" => "error", "rawdata" => ["action" => "PaymentToken Delete", "token" => $vaultedToken->transformToTokenJSON()]];
    }
}

?>