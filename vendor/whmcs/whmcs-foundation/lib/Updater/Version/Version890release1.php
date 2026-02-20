<?php

namespace WHMCS\Updater\Version;

class Version890release1 extends IncrementalVersion
{
    protected $updateActions = ["addRefreshAppsFeedCronTask", "updateModuleGatewayTypePayPalCommerce", "updateModuleGatewayConvertToPayPalCommerce", "updatePayPalCommerceTokenHints"];
    public function __construct(\WHMCS\Version\SemanticVersion $version)
    {
        parent::__construct($version);
        $this->filesToRemove[] = ROOTDIR . "/modules/gateways/paypal_acdc/paypal_acdc.js";
        $this->filesToRemove[] = ROOTDIR . "/modules/gateways/paypal_acdc/paypal_acdc.min.js";
    }
    protected function addRefreshAppsFeedCronTask() : \self
    {
        \WHMCS\Cron\Task\RefreshAppsFeed::register();
        return $this;
    }
    public function updateModuleGatewayTypePayPalCommerce() : \self
    {
        $module = \WHMCS\Module\Gateway\paypal_ppcpv\PayPalCommerce::MODULE_NAME;
        if(!in_array($module, \WHMCS\Module\GatewaySetting::getActiveGatewayModules())) {
            return $this;
        }
        if(\WHMCS\Module\GatewaySetting::getTypeFor($module) == \WHMCS\Module\Gateway::GATEWAY_CREDIT_CARD) {
            return $this;
        }
        \WHMCS\Module\GatewaySetting::setValue($module, "type", \WHMCS\Module\Gateway::GATEWAY_CREDIT_CARD);
        return $this;
    }
    public function updateModuleGatewayConvertToPayPalCommerce() : \self
    {
        $targetGatewayModules = [\WHMCS\Module\Gateway\paypal_ppcpv\PayPalCommerce::MODULE_NAME, \WHMCS\Module\Gateway\paypal_acdc\Core::MODULE_NAME];
        $activeGatewayModules = \WHMCS\Module\GatewaySetting::getActiveGatewayModules();
        foreach ($targetGatewayModules as $targetGatewayModule) {
            if(!in_array($targetGatewayModule, $activeGatewayModules) || !\WHMCS\Module\GatewaySetting::getConvertToFor($targetGatewayModule)) {
            } else {
                \WHMCS\Module\GatewaySetting::setValue($targetGatewayModule, "convertto", "");
            }
        }
        return $this;
    }
    public function updatePayPalCommerceTokenHints() : \self
    {
        foreach (\WHMCS\Database\Capsule::table("tblpaymethods")->select(["id", "description"])->where("gateway_name", "paypal_ppcpv")->get() as $payMethod) {
            if($payMethod->description != "" && filter_var($payMethod->description, FILTER_VALIDATE_EMAIL)) {
                \WHMCS\Database\Capsule::table("tblcreditcards")->where("pay_method_id", $payMethod->id)->where("card_type", "PayPal")->where("last_four", "")->update(["last_four" => $payMethod->description]);
                \WHMCS\Database\Capsule::table("tblpaymethods")->where("id", $payMethod->id)->update(["description" => ""]);
            }
        }
        return $this;
    }
}

?>