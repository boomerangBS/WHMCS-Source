<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Authorization\Rbac;

trait PermissionTrait
{
    protected $permissionData = [];
    public function setData(array $data = [])
    {
        $this->permissionData = $data;
    }
    public function getData()
    {
        if(!is_array($this->permissionData)) {
            $this->permissionData = [];
        }
        return $this->permissionData;
    }
    public function isAllowed($item)
    {
        if(!empty($this->getData()[$item])) {
            return true;
        }
        return false;
    }
    public function listAll()
    {
        return $this->getData();
    }
}

?>