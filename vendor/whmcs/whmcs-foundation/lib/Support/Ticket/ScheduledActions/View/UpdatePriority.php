<?php

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