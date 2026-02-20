<?php


namespace WHMCS\Module\Gateway\paypal_ppcpv\API;
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