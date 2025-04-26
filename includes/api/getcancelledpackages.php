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
$result = select_query("`tblcancelrequests", "COUNT(*)", NULL);
$data = mysql_fetch_array($result);
$totalresults = $data[0];
$query = "SELECT * FROM tblcancelrequests LIMIT " . (int) $limitstart . "," . (int) $limitnum;
$result2 = full_query($query);
$apiresults = ["result" => "success", "totalresults" => $totalresults, "startnumber" => $limitstart, "numreturned" => mysql_num_rows($result2), "packages" => []];
while ($data = mysql_fetch_assoc($result2)) {
    $apiresults["packages"]["package"][] = $data;
}
$responsetype = "xml";

?>