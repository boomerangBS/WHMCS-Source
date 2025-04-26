<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Support\Ticket\ScheduledActions\View;

class TicketScheduledActionsList extends \WHMCS\View\Composite\CompositeView
{
    public function getTemplate()
    {
        return ScheduledActions::TEMPLATE_BASE_PATH . "/listTable";
    }
    public function setTicket(\WHMCS\Support\Ticket $ticket) : \self
    {
        return $this->with(["ticket" => $ticket]);
    }
    public function transformScheduledActionsOfTicket(\Illuminate\Support\Collection $scheduledActionsOfTicket) : array
    {
        return (new \WHMCS\Table\TicketActionsTable())->asRowsStruct($scheduledActionsOfTicket);
    }
    public function render()
    {
        $this->with(["scheduledActionsOfTicket" => $this->transformScheduledActionsOfTicket($this->data()->get("ticket")->scheduledActions()->orderByExecution()->get())]);
        return parent::render();
    }
}

?>