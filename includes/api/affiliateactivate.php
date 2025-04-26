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
if(!function_exists("getAdminName")) {
    require ROOTDIR . "/includes/adminfunctions.php";
}
if(!function_exists("affiliateActivate")) {
    require ROOTDIR . "/includes/affiliatefunctions.php";
}
$result = select_query("tblclients", "id", ["id" => $userid]);
$data = mysql_fetch_array($result);
$userid = $data["id"];
if(!$userid) {
    $apiresults = ["result" => "error", "message" => "Client ID not found"];
} else {
    affiliateActivate($userid);
    $apiresults = ["result" => "success"];
}

?>