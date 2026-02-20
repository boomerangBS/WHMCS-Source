<?php

namespace WHMCS\Module\Addon\ProjectManagement;

class ComplexPermission
{
    private $permission;
    private $permissionDependencies = ["Associate Invoice" => "List Invoices", "Associate Tickets" => "List Support Tickets"];
    public function __construct(Permission $permission)
    {
        $this->permission = $permission;
    }
    public function check($permissionName)
    {
        $addonPermissionName = $permissionName;
        $haveAddonPermission = false;
        $isAddonPermission = false;
        if(in_array($addonPermissionName, Permission::getPermissionList())) {
            $haveAddonPermission = $this->permission->check($addonPermissionName);
            $adminPermissionName = $this->getAdminPermission($addonPermissionName);
            $isAddonPermission = true;
        } else {
            $adminPermissionName = $addonPermissionName;
        }
        $haveAdminPermission = false;
        $isAdminPermission = false;
        if(in_array($adminPermissionName, \WHMCS\User\Admin\Permission::all())) {
            $haveAdminPermission = \WHMCS\User\Admin\Permission::currentAdminHasPermissionName($adminPermissionName);
            $isAdminPermission = true;
        }
        $haveAddonPermission or $addonPermits = $haveAddonPermission || !$isAddonPermission;
        $haveAdminPermission or $platformPermits = $haveAdminPermission || !$isAdminPermission;
        $isAdminPermission or $knownPermission = $isAdminPermission || $isAddonPermission;
        return $knownPermission && $addonPermits && $platformPermits;
    }
    private function getAdminPermission($addonPermission)
    {
        return $this->permissionDependencies[$addonPermission] ?? "";
    }
}

?>