<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
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