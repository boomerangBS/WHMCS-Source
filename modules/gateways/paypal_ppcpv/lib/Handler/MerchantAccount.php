<?php

namespace WHMCS\Module\Gateway\paypal_ppcpv\Handler;

class MerchantAccount extends AbstractHandler
{
    public function getBalances() : \WHMCS\Module\Gateway\BalanceCollection
    {
        $api = $this->api();
        $response = $api->send(new \WHMCS\Module\Gateway\paypal_ppcpv\API\AccountBalanceRequest($api));
        if(!$response instanceof \WHMCS\Module\Gateway\paypal_ppcpv\API\AccountBalanceResponse) {
            return NULL;
        }
        $balances = new \WHMCS\Module\Gateway\BalanceCollection();
        foreach ($response->balances as $balance) {
            $balances->addBalance(\WHMCS\Module\Gateway\Balance::factory($balance->available_balance->value, $balance->available_balance->currency_code));
            $balances->addBalance(\WHMCS\Module\Gateway\PendingBalance::factory($balance->withheld_balance->value, $balance->withheld_balance->currency_code));
        }
        return $balances;
    }
}

?>