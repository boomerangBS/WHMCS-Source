<?php

namespace WHMCS\Module\Addon\ProjectManagement;

abstract class BaseProjectEntity
{
    public $project;
    public function __construct(Project $project)
    {
        $this->project = $project;
    }
    public function project()
    {
        return $this->project;
    }
}

?>