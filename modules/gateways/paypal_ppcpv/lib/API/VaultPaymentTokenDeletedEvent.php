<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

namespace WHMCS\Module\Gateway\paypal_ppcpv\API;

// Decoded file for php version 72.
class VaultPaymentTokenDeletedEvent extends AbstractWebhookEvent
{
    public $id;
    protected $expectedPayloadProperties = ["id"];
    public function vaultedId()
    {
        return $this->id;
    }
    public function getHandler() : \WHMCS\Module\Gateway\paypal_ppcpv\Handler\Event\AbstractWebhookHandler
    {
        return new \WHMCS\Module\Gateway\paypal_ppcpv\Handler\Event\VaultPaymentTokenDeleted();
    }
    public function initiatingModule()
    {
        return \WHMCS\Module\Gateway\paypal_ppcpv\PayPalCommerce::MODULE_NAME;
    }
    public function packEventRequest()
    {
        $payload = json_decode(parent::packEventRequest());
        unset($payload->resource->id);
        foreach ($payload->links as $link) {
            unset($link->href);
        }
        return json_encode($payload);
    }
}

?>