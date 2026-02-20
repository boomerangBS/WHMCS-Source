<?php

namespace WHMCS\Module\Gateway\paypal_acdc;

class ModuleConfiguration extends \WHMCS\Module\Gateway\paypal_ppcpv\ModuleConfiguration
{
    public static function persistedSettings() : array
    {
        $parentSettings = parent::persistedSettings();
        $doNotInherit = ["type"];
        foreach ($doNotInherit as $setting) {
            unset($parentSettings[$setting]);
        }
        foreach (\WHMCS\Module\GatewaySetting::getForGateway(Core::MODULE_NAME) as $setting => $value) {
            $parentSettings[$setting] = $value;
        }
        return $parentSettings;
    }
    public function persist(\WHMCS\Module\Gateway $module)
    {
        throw new \RuntimeException("persistence not available for module extension");
    }
    protected function fieldGatewayName()
    {
        $f = parent::fieldGatewayName();
        $f->defaultValue = Core::DISPLAY_NAME;
        return $f;
    }
}

?>