<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Module\Gateway\paypal_ppcpv\Handler;

class ShowOnOrder extends AbstractHandler
{
    public function handle($submittedParams) : void
    {
        if(isset($submittedParams["visible"])) {
            if($submittedParams["visible"] == "on") {
                $this->assertMerchantStatus($submittedParams["useSandbox"] ?? "");
            } else {
                $this->assertChildModuleInactive($submittedParams["name"] ?? "");
            }
        }
    }
    private function assertMerchantStatus($useSandbox) : ShowOnOrder
    {
        $intendedConfiguration = clone $this->moduleConfiguration;
        $intendedConfiguration->useSandbox = $useSandbox == "on";
        $intendedEnvironment = \WHMCS\Module\Gateway\paypal_ppcpv\Environment::factory($intendedConfiguration);
        if(!$intendedEnvironment->hasCredentials()) {
            throw new \WHMCS\Exception\Module\InvalidConfiguration(\AdminLang::trans("paypalCommerce.credentialsRequiredWhenVisible"));
        }
        return $this;
    }
    private function assertChildModuleInactive($moduleName) : ShowOnOrder
    {
        $cards = new \WHMCS\Module\Gateway();
        if($cards->load(\WHMCS\Module\Gateway\paypal_acdc\Core::MODULE_NAME) && $cards->isLoadedModuleActive() && ($cards->getParams()["visible"] ?? "") == "on") {
            throw new \WHMCS\Exception\Module\InvalidConfiguration(\AdminLang::trans("paypalCommerce.visibleRequiredWhenModuleAdvCards", [":required_module" => $moduleName, ":subordinate_module" => $cards->getDisplayName(), ":action" => \AdminLang::trans("global.disable"), ":action_module" => $cards->getDisplayName()]));
        }
        unset($cards);
        return $this;
    }
}

?>