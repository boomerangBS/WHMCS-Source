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
$totalresults = WHMCS\Database\Capsule::table("tblticketpredefinedcats")->count();
$apiresults = ["result" => "success", "totalresults" => $totalresults];
$result = full_query("SELECT c.*, COUNT(r.id) AS replycount FROM tblticketpredefinedcats c LEFT JOIN tblticketpredefinedreplies r ON c.id=r.catid GROUP BY c.id ORDER BY c.name ASC");
while ($data = mysql_fetch_assoc($result)) {
    $apiresults["categories"]["category"][] = $data;
}
$responsetype = "xml";

?>