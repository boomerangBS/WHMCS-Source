<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Payment\PayMethod;

class Collection extends \Illuminate\Database\Eloquent\Collection
{
    public function forGateway($gatewayModule)
    {
        $gateway = new \WHMCS\Module\Gateway();
        $isCcGateway = $isBankGateway = false;
        $noLocalCards = false;
        if($gateway->load($gatewayModule)) {
            $type = $gateway->getParam("type");
            $isCcGateway = $type == \WHMCS\Module\Gateway::GATEWAY_CREDIT_CARD && !$gateway->functionExists("no_cc");
            $isBankGateway = $type == \WHMCS\Module\Gateway::GATEWAY_BANK;
            $noLocalCards = $gateway->functionExists("nolocalcc");
        }
        return $this->filter(function (\WHMCS\Payment\Contracts\PayMethodInterface $adapter) use($gatewayModule, $isCcGateway, $isBankGateway, $noLocalCards) {
            if($adapter->getGateway() && $adapter->getGateway()->getLoadedModule() === $gatewayModule) {
                return true;
            }
            if($isCcGateway && !$noLocalCards) {
                return $adapter->isLocalCreditCard();
            }
            if($isBankGateway) {
                return $adapter->isBankAccount();
            }
            return false;
        });
    }
    public function creditCards()
    {
        return $this->filter(function (\WHMCS\Payment\Contracts\PayMethodInterface $adapter) {
            return $adapter->isCreditCard();
        });
    }
    public function localCreditCards()
    {
        return $this->filter(function (\WHMCS\Payment\Contracts\PayMethodInterface $adapter) {
            return $adapter->getType() === \WHMCS\Payment\Contracts\PayMethodTypeInterface::TYPE_CREDITCARD_LOCAL;
        });
    }
    public function bankAccounts()
    {
        return $this->filter(function (\WHMCS\Payment\Contracts\PayMethodInterface $adapter) {
            return in_array($adapter->getType(), [\WHMCS\Payment\Contracts\PayMethodTypeInterface::TYPE_BANK_ACCOUNT, \WHMCS\Payment\Contracts\PayMethodTypeInterface::TYPE_REMOTE_BANK_ACCOUNT]);
        });
    }
    public function validateGateways()
    {
        return $this->filter(function (Model $payMethod) {
            return !$payMethod->isUsingInactiveGateway();
        });
    }
    public function sortByExpiryDate($expiringFirst = false)
    {
        return $this->sort(function (Model $payMethod1, Model $payMethod2) use($expiringFirst) {
            if(!$payMethod1->isCreditCard() || !$payMethod2->isCreditCard()) {
                return 0;
            }
            $expiryDate1 = $payMethod1->payment->getExpiryDate();
            $expiryDate2 = $payMethod2->payment->getExpiryDate();
            $diff = ($expiryDate2 ? $expiryDate2->getTimestamp() : 0) - ($expiryDate1 ? $expiryDate1->getTimestamp() : 0);
            if($expiringFirst) {
                $diff = -1 * $diff;
            }
            return $diff;
        });
    }
}

?>