<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Support\Ticket\Actions;

class AssignTicket extends AbstractAction
{
    use JSONParametersTrait;
    public $assignAdminId = 0;
    public static $name = "AssignTicket";
    public function init(\WHMCS\Support\Ticket $ticket, array $parameters)
    {
        $this->ticket = $ticket;
        $this->assignAdminId = (int) $parameters["assignAdminId"];
        return $this;
    }
    public function execute()
    {
        if($this->assignAdminId === $this->getTicket()->flaggedAdminId) {
            return true;
        }
        $ticket = new \WHMCS\Tickets();
        $ticket->setID($this->ticket->id);
        if(!array_key_exists($this->assignAdminId, $ticket->getFlaggableStaff())) {
            throw new \InvalidArgumentException("Admin does not exist or is inactive");
        }
        return $ticket->setFlagTo($this->assignAdminId);
    }
    public function assertParameters(array $parameters)
    {
        if(!isset($parameters["assignAdminId"]) || !is_numeric($parameters["assignAdminId"])) {
            throw new \InvalidArgumentException("assignAdminId parameter must be a number.");
        }
        return $this;
    }
    public function detailString()
    {
        $admin = \WHMCS\User\Admin::find($this->assignAdminId);
        if(is_null($admin)) {
            return "";
        }
        return $admin->fullName;
    }
}

?>