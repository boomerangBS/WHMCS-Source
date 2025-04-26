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
if(!$days) {
    $days = 7;
}
if(!$expires) {
    $expires = date("YmdHis", mktime(date("H"), date("i"), date("s"), date("m"), date("d") + $days, date("Y")));
}
$banid = insert_query("tblbannedips", ["ip" => $ip, "reason" => $reason, "expires" => $expires]);
$apiresults = ["result" => "success", "banid" => $banid];

?>