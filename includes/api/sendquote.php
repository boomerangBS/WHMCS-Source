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
if(!function_exists("getClientsDetails")) {
    require ROOTDIR . "/includes/clientfunctions.php";
}
if(!function_exists("updateInvoiceTotal")) {
    require ROOTDIR . "/includes/invoicefunctions.php";
}
if(!function_exists("sendQuotePDF")) {
    require ROOTDIR . "/includes/quotefunctions.php";
}
$result = select_query("tblquotes", "", ["id" => $quoteid]);
$data = mysql_fetch_array($result);
$quoteid = $data["id"];
if(!$quoteid) {
    $apiresults = ["result" => "error", "message" => "Quote ID Not Found"];
} else {
    sendQuotePDF($quoteid);
    $apiresults = ["result" => "success"];
}

?>