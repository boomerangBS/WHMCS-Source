<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Support\Ticket\ScheduledActions\View;

class UpdatePriority extends \WHMCS\View\Composite\CompositeView
{
    public function viewFor()
    {
        return "WHMCS\\Support\\Ticket\\Actions\\UpdatePriority";
    }
    public function getTemplate()
    {
        return ScheduledActions::TEMPLATE_BASE_PATH . "/actions/updatepriority";
    }
    public function setTicketPriorities($priorities) : \self
    {
        return $this->with(["ticketPriorities" => collect($priorities)]);
    }
    public function withSelectedPriority($priorityIdentifier) : \self
    {
        return $this->with(["selectedPriority" => $priorityIdentifier]);
    }
    public function init()
    {
        return parent::init()->setTicketPriorities(\WHMCS\Support\Ticket::getPriorities())->withSelectedPriority("")->with(["actionContainer" => (new ActionContainer($this->viewFor()))->init(), "actionName" => $this->viewFor()::$name]);
    }
}

?>