<?php

namespace WHMCS\Support\Repository;

class DepartmentRepository
{
    public function loadByIds($ids = ["*"], array $columns) : \Illuminate\Database\Eloquent\Collection
    {
        return \WHMCS\Support\Department::query()->whereIn("id", $ids)->get($columns);
    }
}

?>