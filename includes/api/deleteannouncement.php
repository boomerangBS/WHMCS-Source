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
$result = select_query("tblannouncements", "id", ["id" => $announcementid]);
$data = mysql_fetch_array($result);
if(!$data["id"]) {
    $apiresults = ["result" => "error", "message" => "Announcement ID Not Found"];
    return false;
}
delete_query("tblannouncements", ["id" => $announcementid]);
delete_query("tblannouncements", ["parentid" => $announcementid]);
$apiresults = ["result" => "success", "announcementid" => $announcementid];

?>