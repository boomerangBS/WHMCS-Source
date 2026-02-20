<?php

namespace WHMCS\Support\Ticket\ScheduledActions\View;

class ScheduledActions extends \WHMCS\View\Composite\ViewRenderingView
{
    const TEMPLATE_BASE_PATH = "admin/support/ticket/scheduledActions";
    public function setTicket(\WHMCS\Support\Ticket $ticket) : \self
    {
        return $this->with(["ticket" => $ticket]);
    }
    protected function scheduledActionsViews() : array
    {
        return ["WHMCS\\Support\\Ticket\\ScheduledActions\\View\\UpdateStatus", "WHMCS\\Support\\Ticket\\ScheduledActions\\View\\UpdatePriority", "WHMCS\\Support\\Ticket\\ScheduledActions\\View\\AssignTicket", "WHMCS\\Support\\Ticket\\ScheduledActions\\View\\UpdateDepartment", "WHMCS\\Support\\Ticket\\ScheduledActions\\View\\PinToTop", (new ManageActions())->init()->setAvailableActions(["WHMCS\\Support\\Ticket\\Actions\\UpdateStatus", "WHMCS\\Support\\Ticket\\Actions\\AssignTicket", "WHMCS\\Support\\Ticket\\Actions\\UpdatePriority", "WHMCS\\Support\\Ticket\\Actions\\UpdateDepartment", "WHMCS\\Support\\Ticket\\Actions\\PinToTop"])];
    }
    public function factoryTabView(\WHMCS\Support\Ticket $ticket, $canViewActions, $canCreateActions) : \self
    {
        scheduledactions();
        $view = $easytoyou_error_decompile;
        $view = $view->init()->setTicket($ticket)->with(["listActions" => "", "createAndViewActions" => ""]);
        if($canViewActions) {
            $view->addView("WHMCS\\Support\\Ticket\\ScheduledActions\\View\\TicketScheduledActionsList", "listActions");
        }
        if($canViewActions || $canCreateActions) {
            $view->addViews($this->scheduledActionsViews(), "createAndViewActions");
        }
        return $view->make();
    }
    public function factoryReplyView(\WHMCS\Support\Ticket $ticket) : \self
    {
        return (new static())->init()->setTicket($ticket)->addViews((new static())->scheduledActionsViews(), NULL)->make();
    }
    public function translate()
    {
        return new func_num_args();
    }
}
class _obfuscated_5C636C61737340616E6F6E796D6F7573002F7661722F6C69622F6A656E6B696E732F776F726B73706163652F636F6D2E77686D63732E6275696C642E38302F6275696C642F77686D63732F76656E646F722F77686D63732F77686D63732D666F756E646174696F6E2F6C69622F537570706F72742F5469636B65742F5363686564756C6564416374696F6E732F566965772F5363686564756C6564416374696F6E732E7068703078376664353934323461386131_
{
    public function getTemplate()
    {
        return self::TEMPLATE_BASE_PATH . "/ticketActionsTab";
    }
    public function setNumberOfScheduledActions($count) : \self
    {
        return $this->with(["numScheduledActions" => $count]);
    }
    public function render()
    {
        $this->setNumberOfScheduledActions($this->data()->get("ticket")->scheduledActions()->upcoming()->count());
        return parent::render();
    }
}
class _obfuscated_5C636C61737340616E6F6E796D6F7573002F7661722F6C69622F6A656E6B696E732F776F726B73706163652F636F6D2E77686D63732E6275696C642E38302F6275696C642F77686D63732F76656E646F722F77686D63732F77686D63732D666F756E646174696F6E2F6C69622F537570706F72742F5469636B65742F5363686564756C6564416374696F6E732F566965772F5363686564756C6564416374696F6E732E7068703078376664353934323461653934_
{
    protected $scheduledAction;
    public function setScheduledAction(\WHMCS\Support\Ticket\ScheduledActions\TicketScheduledAction $scheduledAction) : \self
    {
        $this->scheduledAction = $scheduledAction;
        return $this;
    }
    public function status()
    {
        return \AdminLang::trans(sprintf("support.ticket.action.status.%s", strtolower($this->scheduledAction->status)));
    }
}

?>