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
$where = [];
if(!empty($clientid)) {
    $where["userid"] = $clientid;
}
if(!empty($invoiceid)) {
    $where["invoiceid"] = $invoiceid;
}
if(!empty($transid)) {
    $where["transid"] = $transid;
}
$result = select_query("tblaccounts", "", $where);
$apiresults = ["result" => "success", "totalresults" => mysql_num_rows($result), "startnumber" => 0, "numreturned" => mysql_num_rows($result)];
while ($data = mysql_fetch_assoc($result)) {
    $apiresults["transactions"]["transaction"][] = $data;
}
$responsetype = "xml";

?>