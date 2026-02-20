<?php

namespace WHMCS\Support\Ticket\Observers;

class TicketNotificationTicketObserver
{
    public function deleting(\WHMCS\Support\Ticket $ticket) : void
    {
        $ticket->notifications()->delete();
    }
}

?>