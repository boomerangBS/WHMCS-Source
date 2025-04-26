<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Authorization\Rbac;

trait RoleTrait
{
    use PermissionTrait;
    public function allow(array $itemsToAllow = [])
    {
        $itemsToAllow = array_filter($itemsToAllow);
        if(empty($itemsToAllow)) {
            return $this;
        }
        $data = $this->getData();
        foreach ($itemsToAllow as $item) {
            if(is_string($item)) {
                $data[$item] = 1;
            }
        }
        $this->setData($data);
        return $this;
    }
    public function deny(array $itemsToDeny = [])
    {
        $itemsToDeny = array_filter($itemsToDeny);
        if(empty($itemsToDeny)) {
            return $this;
        }
        $data = $this->getData();
        foreach ($itemsToDeny as $item) {
            if(is_string($item)) {
                $data[$item] = 0;
            }
        }
        $this->setData($data);
        return $this;
    }
}

?>