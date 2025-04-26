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
$ticketId = App::getFromRequest("ticketid");
$delete = (bool) App::getFromRequest("delete");
if(!$ticketId) {
    $apiresults = ["result" => "error", "message" => "Ticket ID Required"];
} else {
    $ticket = WHMCS\Database\Capsule::table("tbltickets")->find($ticketId);
    if(!$ticket) {
        $apiresults = ["result" => "error", "message" => "Ticket ID Not Found"];
    } elseif($ticket->userid) {
        $apiresults = ["result" => "error", "message" => "A Client Cannot Be Blocked"];
    } else {
        $email = $ticket->email;
        if(!$email) {
            $apiresults = ["result" => "error", "message" => "Missing Email Address"];
        } else {
            $blockedAlready = WHMCS\Database\Capsule::table("tblticketspamfilters")->where("type", "sender")->where("content", $email)->count();
            if($blockedAlready === 0) {
                WHMCS\Database\Capsule::table("tblticketspamfilters")->insert(["type" => "sender", "content" => $email]);
            }
            $apiresults = ["result" => "success", "deleted" => false];
            if($delete) {
                if(!function_exists("deleteTicket")) {
                    require ROOTDIR . "/includes/ticketfunctions.php";
                }
                try {
                    deleteTicket($ticketId);
                    $apiresults["deleted"] = true;
                } catch (Exception $e) {
                    $apiresults = ["result" => "error", "message" => $e->getMessage()];
                    return NULL;
                }
            }
        }
    }
}

?>