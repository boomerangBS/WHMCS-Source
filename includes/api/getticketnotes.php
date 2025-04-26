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
$notes = [];
$result = select_query("tblticketnotes", "id,admin,date,message,attachments,attachments_removed", ["ticketid" => $ticketid], "date", "ASC");
while ($data = mysql_fetch_assoc($result)) {
    $data["attachments_removed"] = stringLiteralToBool($data["attachments_removed"]);
    $notes[] = $data;
}
$apiresults = ["result" => "success", "totalresults" => count($notes), "notes" => ["note" => $notes]];
$responsetype = "xml";

?>