<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
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