<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

namespace WHMCS\Module\Gateway\paypal_ppcpv;

// Decoded file for php version 72.
class MerchantStatusSetting extends \WHMCS\Utility\Bitmask
{
    const BIT_COUNT = 4;
    const PAYMENTS_RECEIVABLE = 1;
    const EMAIL_VERIFIED = 2;
    const VAULTING_CAPABLE = 4;
    const ADVANCED_CARDS_CAPABLE = 8;
    public function paymentsReceivable()
    {
        return $this->has(static::PAYMENTS_RECEIVABLE);
    }
    public function emailVerified()
    {
        return $this->has(static::EMAIL_VERIFIED);
    }
    public function vaultCapable()
    {
        return $this->has(static::VAULTING_CAPABLE);
    }
    public function cardsCapable()
    {
        return $this->has(static::ADVANCED_CARDS_CAPABLE);
    }
    public function fromResponse(API\MerchantStatusResponse $response) : \self
    {
        $this->as(static::PAYMENTS_RECEIVABLE, $response->paymentsReceivable());
        $this->as(static::EMAIL_VERIFIED, $response->primaryEmailConfirmed());
        $this->as(static::VAULTING_CAPABLE, $response->capabilityActive("PAYPAL_WALLET_VAULTING_ADVANCED"));
        $this->as(static::ADVANCED_CARDS_CAPABLE, $response->productSubscribed("PPCP_CUSTOM") && $response->capabilityActive("CUSTOM_CARD_PROCESSING"));
        return $this;
    }
}

?>