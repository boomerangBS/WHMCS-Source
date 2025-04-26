<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Support\Ticket\ScheduledActions\View;

class UpdateStatus extends \WHMCS\View\Composite\CompositeView
{
    public function viewFor()
    {
        return "WHMCS\\Support\\Ticket\\Actions\\UpdateStatus";
    }
    public function getTemplate()
    {
        return ScheduledActions::TEMPLATE_BASE_PATH . "/actions/updatestatus";
    }
    public function setTicketStatuses($statuses) : \self
    {
        return $this->with(["ticketStatuses" => collect($statuses)]);
    }
    public function withSelectedStatus($statusId) : \self
    {
        return $this->with(["selectedStatus" => $statusId]);
    }
    public function init()
    {
        return parent::init()->setTicketStatuses(\WHMCS\Support\Ticket\Status::all())->withSelectedStatus(0)->with(["actionContainer" => (new ActionContainer($this->viewFor()))->init(), "actionName" => $this->viewFor()::$name]);
    }
}

?>