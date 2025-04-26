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
$result = select_query("tblclientgroups", "COUNT(id)", "");
$data = mysql_fetch_array($result);
$totalresults = $data[0];
$apiresults = ["result" => "success", "totalresults" => $totalresults];
$result = select_query("tblclientgroups", "", "", "id", "ASC");
while ($data = mysql_fetch_assoc($result)) {
    $apiresults["groups"]["group"][] = $data;
}
$responsetype = "xml";

?>