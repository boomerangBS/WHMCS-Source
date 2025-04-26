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
$result = select_query("tblclients", "id", ["id" => $clientid]);
$data = mysql_fetch_array($result);
$clientid = $data["id"];
if(!$clientid) {
    $apiresults = ["result" => "error", "message" => "Client ID Not Found"];
} else {
    $credits = [];
    $result = select_query("tblcredit", "id,date,description,amount,relid", ["clientid" => $clientid], "date", "ASC");
    while ($data = mysql_fetch_assoc($result)) {
        $credits[] = $data;
    }
    $apiresults = ["result" => "success", "totalresults" => count($credits), "clientid" => $clientid, "credits" => ["credit" => $credits]];
    $responsetype = "xml";
}

?>