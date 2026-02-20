<?php

namespace WHMCS\Support\Ticket\Actions;

class UpdateDepartment extends AbstractAction
{
    use JSONParametersTrait;
    use OverlayParametersTrait;
    public $department = 0;
    public static $name = "UpdateDepartment";
    public function execute()
    {
        $department = $this->department();
        if(is_null($department)) {
            throw new \InvalidArgumentException("Department does not exist");
        }
        if($department->id === $this->getTicket()->departmentId) {
            return true;
        }
        $ticket = new \WHMCS\Tickets();
        $ticket->setID($this->getTicket()->id);
        return $ticket->setDept($department->id);
    }
    public function department() : \WHMCS\Support\Department
    {
        return \WHMCS\Support\Department::find($this->department);
    }
    public function detailString()
    {
        $department = $this->department();
        if(is_null($department)) {
            return "";
        }
        return $department->name;
    }
}

?>