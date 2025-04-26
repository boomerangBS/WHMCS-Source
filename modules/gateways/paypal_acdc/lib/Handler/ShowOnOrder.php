<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Module\Gateway\paypal_acdc\Handler;

class ShowOnOrder extends AbstractHandler
{
    public function handle($submittedParams) : void
    {
        if(isset($submittedParams["visible"]) && $submittedParams["visible"] == "on") {
            $this->assertMerchantStatus()->assertParentModuleActive($submittedParams["name"] ?? "")->assertRequiredCapabilities();
        }
    }
    private function assertMerchantStatus() : ShowOnOrder
    {
        if(!$this->env()->hasCredentials()) {
            throw new \WHMCS\Exception\Module\InvalidConfiguration(\AdminLang::trans("paypalCommerce.credentialsRequiredWhenVisible"));
        }
        return $this;
    }
    private function assertParentModuleActive($moduleName) : ShowOnOrder
    {
        $commerce = new \WHMCS\Module\Gateway();
        if($commerce->load(\WHMCS\Module\Gateway\paypal_ppcpv\PayPalCommerce::MODULE_NAME) && $commerce->isLoadedModuleActive() && ($commerce->getParams()["visible"] ?? "") != "on") {
            throw new \WHMCS\Exception\Module\InvalidConfiguration(\AdminLang::trans("paypalCommerce.visibleRequiredWhenModuleAdvCards", [":required_module" => $commerce->getDisplayName(), ":subordinate_module" => $moduleName, ":action" => \AdminLang::trans("global.enable"), ":action_module" => $commerce->getDisplayName()]));
        }
        unset($commerce);
        return $this;
    }
    private function assertRequiredCapabilities() : ShowOnOrder
    {
        $config = \WHMCS\Module\Gateway\paypal_acdc\ModuleConfiguration::fromPersistance();
        $status = $config->getMerchantStatus(\WHMCS\Module\Gateway\paypal_ppcpv\Environment::factory($config));
        if(!$status->cardsCapable()) {
            throw new \WHMCS\Exception\Module\InvalidConfiguration(\AdminLang::trans("paypalCommerceAdvCards.missingCapability"));
        }
        unset($config);
        unset($status);
        return $this;
    }
    public function updateVisibility($desiredVisibilityState) : void
    {
        if(!$this->module->isLoadedModuleActive()) {
            return NULL;
        }
        $this->module->saveConfigValue("visible", $desiredVisibilityState);
    }
}

?>