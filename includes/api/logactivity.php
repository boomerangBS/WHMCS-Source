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
$clientId = (int) App::getFromRequest("clientid");
if(!$clientId) {
    $clientId = (int) App::getFromRequest("userid");
}
logActivity($description, $clientId);
$apiresults = ["result" => "success"];

?>