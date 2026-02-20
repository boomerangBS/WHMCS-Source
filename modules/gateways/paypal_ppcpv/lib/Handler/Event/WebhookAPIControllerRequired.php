<?php


namespace WHMCS\Module\Gateway\paypal_ppcpv\Handler\Event;
trait WebhookAPIControllerRequired
{
    protected $api;
    public function setAPI(\WHMCS\Module\Gateway\paypal_ppcpv\API\Controller $api) : \self
    {
        $this->api = $api;
        return $this;
    }
}

?>