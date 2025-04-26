<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
if(!defined("WHMCS")) {
    exit("This file cannot be accessed directly");
}
$ticket = NULL;
if(!empty($ticketid)) {
    $ticket = WHMCS\Support\Ticket::find($ticketid);
}
if(is_null($ticket)) {
    $apiresults = ["result" => "error", "message" => "Ticket ID not found"];
} else {
    if(!function_exists("deleteTicket")) {
        require ROOTDIR . "/includes/ticketfunctions.php";
    }
    deleteTicket($ticket->id);
    $apiresults = ["result" => "success"];
}

?>