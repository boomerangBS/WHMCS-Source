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
$result = select_query("tblticketnotes", "id", ["id" => $noteid]);
$data = mysql_fetch_array($result);
if(!$data["id"]) {
    $apiresults = ["result" => "error", "message" => "Note ID Not Found"];
} else {
    delete_query("tblticketnotes", ["id" => $noteid]);
    $apiresults = ["result" => "success", "noteid" => $noteid];
}

?>