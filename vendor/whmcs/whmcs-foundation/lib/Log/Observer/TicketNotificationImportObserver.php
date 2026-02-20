<?php

namespace WHMCS\Log\Observer;

class TicketNotificationImportObserver
{
    public function created(\WHMCS\Log\TicketImport $ticketImport) : void
    {
        \WHMCS\Support\Ticket\TicketImportNotification::storeEntry($ticketImport);
    }
    public function updated(\WHMCS\Log\TicketImport $ticketImport) : void
    {
        if(!$ticketImport->isPending()) {
            $ticketImport->notification()->delete();
        }
    }
    public function deleting(\WHMCS\Log\TicketImport $ticketImport) : void
    {
        $ticketImport->notification()->delete();
    }
}

?>