<?php

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