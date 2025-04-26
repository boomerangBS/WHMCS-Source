<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Module;

class Notification extends AbstractModule
{
    protected $type = self::TYPE_NOTIFICATION;
    public function getActiveModules()
    {
        return \WHMCS\Database\Capsule::table("tblnotificationproviders")->where("active", "1")->distinct("name")->pluck("name")->all();
    }
    public function getClassPath()
    {
        $module = $this->getLoadedModule();
        return "WHMCS\\Module\\Notification\\" . $module . "\\" . $module;
    }
    public function getAdminActivationForms($moduleName)
    {
        return [(new \WHMCS\View\Form())->setUriByRoutePath("admin-setup-notifications-overview")->setMethod(\WHMCS\View\Form::METHOD_GET)->setParameters(["rp" => "/admin/setup/notifications/overview", "activate" => $moduleName])->setSubmitLabel(\AdminLang::trans("global.activate"))];
    }
    public function getAdminManagementForms($moduleName)
    {
        return [(new \WHMCS\View\Form())->setUriByRoutePath("admin-setup-notifications-overview")->setMethod(\WHMCS\View\Form::METHOD_GET)->setParameters(["rp" => "/admin/setup/notifications/overview"])->setSubmitLabel(\AdminLang::trans("global.manage"))];
    }
}

?>