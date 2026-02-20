<?php

namespace WHMCS\Support\Ticket\ScheduledActions\View;

class UpdateDepartment extends \WHMCS\View\Composite\CompositeView
{
    public function viewFor()
    {
        return "WHMCS\\Support\\Ticket\\Actions\\UpdateDepartment";
    }
    public function getTemplate()
    {
        return ScheduledActions::TEMPLATE_BASE_PATH . "/actions/updatedepartment";
    }
    public function setDepartments($departments) : \self
    {
        return $this->with(["departments" => collect($departments)]);
    }
    public function withSelectedDepartment($departmentId) : \self
    {
        return $this->with(["selectedDepartment" => $departmentId]);
    }
    public function init()
    {
        return parent::init()->setDepartments(\WHMCS\Support\Department::all())->withSelectedDepartment(0)->with(["actionContainer" => (new ActionContainer($this->viewFor()))->init(), "actionName" => $this->viewFor()::$name]);
    }
}

?>