<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Module;

class Security extends AbstractModule
{
    protected $type = self::TYPE_SECURITY;
    public function getActiveModules()
    {
        return (new \WHMCS\TwoFactorAuthentication())->getAvailableModules();
    }
    public function getAdminActivationForms($moduleName)
    {
        return [(new \WHMCS\View\Form())->setUriPrefixAdminBaseUrl("configtwofa.php")->setMethod(\WHMCS\View\Form::METHOD_GET)->setParameters(["module" => $moduleName])->setSubmitLabel(\AdminLang::trans("global.activate"))];
    }
    public function getAdminManagementForms($moduleName)
    {
        return [(new \WHMCS\View\Form())->setUriPrefixAdminBaseUrl("configtwofa.php")->setMethod(\WHMCS\View\Form::METHOD_GET)->setParameters(["module" => $moduleName])->setSubmitLabel(\AdminLang::trans("global.manage"))];
    }
}

?>