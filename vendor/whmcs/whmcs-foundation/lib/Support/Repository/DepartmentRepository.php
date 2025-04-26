<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Support\Repository;

class DepartmentRepository
{
    public function loadByIds($ids = ["*"], array $columns) : \Illuminate\Database\Eloquent\Collection
    {
        return \WHMCS\Support\Department::query()->whereIn("id", $ids)->get($columns);
    }
}

?>