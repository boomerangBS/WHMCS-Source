<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

namespace WHMCS\Admin\ApplicationSupport\View\Traits;

// Decoded file for php version 72.
trait AdminUserContextTrait
{
    protected $adminUser;
    public function getAdminUser()
    {
        if(!$this->adminUser) {
            $id = \WHMCS\Session::get("adminid");
            if($id) {
                $user = \WHMCS\User\Admin::find($id);
            } else {
                $user = new \WHMCS\User\Admin();
            }
            $this->setAdminUser($user);
        }
        return $this->adminUser;
    }
    public function setAdminUser($user)
    {
        $this->adminUser = $user;
        return $this;
    }
    public function getAdminTemplateVariables()
    {
        $user = $this->getAdminUser();
        $adminUsername = $user->firstName . " " . $user->lastName;
        $data = ["adminid" => $user->id, "admin_username" => ucfirst($adminUsername), "adminFullName" => $adminUsername, "admin_notes" => $user->notes, "admin_supportDepartmentIds" => $user->supportDepartmentIds, "admin_perms" => $user->getRolePermissions(), "addon_modules" => $user->getModulePermissions(), "adminLanguage" => $user->language, "isFullAdmin" => $this->isFullAdmin(), "adminTemplateTheme" => $user->templateThemeName, "adminBaseRoutePath" => \WHMCS\Admin\AdminServiceProvider::getAdminRouteBase()];
        return $data;
    }
    public function getAdminLanguageVariables()
    {
        \AdminLang::self();
        global $_ADMINLANG;
        return $_ADMINLANG;
    }
    public function getOnlineAdminUsernames() : array
    {
        $storage = new \WHMCS\TransientData();
        $adminsOnline = $storage->retrieve("widget.Staff");
        if(!is_null($adminsOnline)) {
            $adminsOnline = json_decode($adminsOnline);
        } else {
            $adminsOnline = \WHMCS\User\AdminLog::with("admin")->online()->get();
            $storage->store("widget.Staff", json_encode(\WHMCS\Input\Sanitize::makeSafeForOutput($adminsOnline)), 60);
        }
        $adminUsernames = [];
        foreach ($adminsOnline as $adminOnline) {
            $adminUsernames[] = $adminOnline->adminusername;
        }
        return $adminUsernames;
    }
    public function isFullAdmin()
    {
        $user = $this->getAdminUser();
        if($user && $user->hasPermission("Configure General Settings")) {
            return true;
        }
        return false;
    }
}

?>