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
if(isset($code) && $code) {
    $where["code"] = (string) $code;
} elseif(isset($id) && $id) {
    $where["id"] = (int) $id;
}
$result = select_query("tblpromotions", "", $where, "code", "ASC");
$apiresults = ["result" => "success", "totalresults" => mysql_num_rows($result)];
while ($data = mysql_fetch_assoc($result)) {
    $apiresults["promotions"]["promotion"][] = $data;
}
$responsetype = "xml";

?>