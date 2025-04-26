<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Updater\Version;

class Version8120release1 extends IncrementalVersion
{
    protected $updateActions = ["convertAssignTicketAssignIdParameterToInt"];
    public function convertAssignTicketAssignIdParameterToInt() : void
    {
        \WHMCS\Support\Ticket\ScheduledActions\TicketScheduledAction::where("action", "AssignTicket")->each(function ($action) {
            $assignTicket = new \WHMCS\Support\Ticket\Actions\AssignTicket();
            $assignTicket->unserializeParameters($action->parameters);
            $action->parameters = $assignTicket->serializeParameters();
            $action->isDirty() or $action->isDirty() && $action->save();
        });
    }
}

?>