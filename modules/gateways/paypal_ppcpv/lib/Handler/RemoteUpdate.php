<?php

namespace WHMCS\Module\Gateway\paypal_ppcpv\Handler;

class RemoteUpdate extends AbstractHandler
{
    public function adminEditPaymentMethod($renderSource, $payMethod) : \WHMCS\Payment\Contracts\PayMethodInterface
    {
        $module = \WHMCS\Module\Gateway\paypal_ppcpv\PayPalCommerce::MODULE_NAME;
        return moduleView($module, "admin.paymethod_edit", ["renderSource" => $renderSource, "module" => $module, "moduleDisplayName" => $this->moduleConfiguration->getGatewayName(), "payMethod" => $payMethod]);
    }
    public function clientEditPaymentMethod($renderSource, $payMethod) : \WHMCS\Payment\Contracts\PayMethodInterface
    {
        $module = \WHMCS\Module\Gateway\paypal_ppcpv\PayPalCommerce::MODULE_NAME;
        return moduleView($module, "client.paymethod_edit", ["renderSource" => $renderSource, "module" => $module, "payMethod" => $payMethod]);
    }
    public static function assertRenderSource($params)
    {
        if(!isset($params["_source"]) || strlen($params["_source"]) == 0) {
            throw new \RuntimeException("Unknown calling _source for credit_card_input");
        }
        return $params["_source"];
    }
}

?>