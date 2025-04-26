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
if(!function_exists("captureCCPayment")) {
    require ROOTDIR . "/includes/ccfunctions.php";
}
if(!function_exists("getClientsDetails")) {
    require ROOTDIR . "/includes/clientfunctions.php";
}
if(!function_exists("processPaidInvoice")) {
    require ROOTDIR . "/includes/invoicefunctions.php";
}
$result = select_query("tblinvoices", "id", ["id" => $invoiceid, "status" => "Unpaid"]);
$data = mysql_fetch_array($result);
if(!is_array($data) || empty($data["id"])) {
    $apiresults = ["result" => "error", "message" => "Invoice Not Found or Not Unpaid"];
} else {
    $ccResult = captureCCPayment($invoiceid, $cvv ?? NULL);
    if(is_string($ccResult) && $ccResult == "success" || is_string($ccResult) && $ccResult == "pending" || is_bool($ccResult) && $ccResult) {
        $apiresults = ["result" => "success"];
    } else {
        $apiresults = ["result" => "error", "message" => "Payment Attempt Failed"];
    }
}

?>