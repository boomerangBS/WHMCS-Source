<?php


namespace WHMCS\Module\Gateway\paypal_ppcpv\API;
abstract class SimpleGetRequest extends AbstractRequest
{
    public function sendReady()
    {
        return true;
    }
    protected function payload()
    {
        return "";
    }
}

?>