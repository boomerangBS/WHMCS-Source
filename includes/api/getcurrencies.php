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
$result = select_query("tblcurrencies", "", "", "id", "ASC");
$apiresults = ["result" => "success", "totalresults" => mysql_num_rows($result)];
while ($data = mysql_fetch_array($result)) {
    $id = $data["id"];
    $code = $data["code"];
    $prefix = $data["prefix"];
    $suffix = $data["suffix"];
    $format = $data["format"];
    $rate = $data["rate"];
    $apiresults["currencies"]["currency"][] = ["id" => $id, "code" => $code, "prefix" => $prefix, "suffix" => $suffix, "format" => $format, "rate" => $rate];
}
$responsetype = "xml";

?>