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
if(!$limitstart) {
    $limitstart = 0;
}
if(!$limitnum) {
    $limitnum = 25;
}
$result = select_query("tblannouncements", "COUNT(*)", "");
$data = mysql_fetch_array($result);
$totalresults = $data[0];
$result = select_query("tblannouncements", "", "", "date", "DESC", $limitstart . "," . $limitnum);
$apiresults = ["result" => "success", "totalresults" => $totalresults, "startnumber" => $limitstart, "numreturned" => mysql_num_rows($result)];
while ($data = mysql_fetch_assoc($result)) {
    $apiresults["announcements"]["announcement"][] = $data;
}
$responsetype = "xml";

?>