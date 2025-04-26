<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Support\Ticket\ScheduledActions\View;

class AssignTicket extends \WHMCS\View\Composite\CompositeView
{
    public function viewFor()
    {
        return "WHMCS\\Support\\Ticket\\Actions\\AssignTicket";
    }
    public function getTemplate()
    {
        return ScheduledActions::TEMPLATE_BASE_PATH . "/actions/assignticket";
    }
    public function setAssignableAdmin($admins) : \self
    {
        return $this->with(["assignableAdmins" => collect($admins)]);
    }
    public function withSelectedAdmin($adminId) : \self
    {
        return $this->with(["selectedAdmin" => $adminId]);
    }
    public function init()
    {
        return parent::init()->setAssignableAdmin((new \WHMCS\Tickets())->getFlaggableStaff())->withSelectedAdmin(0)->with(["actionContainer" => (new ActionContainer($this->viewFor()))->init(), "actionName" => $this->viewFor()::$name]);
    }
}

?>