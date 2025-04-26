<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Support\Services;

class DepartmentService
{
    protected $departmentRepository;
    public function __construct(\WHMCS\Support\Repository\DepartmentRepository $departmentRepository)
    {
        $this->departmentRepository = $departmentRepository;
    }
    public function getDepartmentNames($ids) : array
    {
        return $this->departmentRepository->loadByIds($ids, ["name"])->pluck("name")->toArray();
    }
}

?>