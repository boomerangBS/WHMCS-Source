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
if(!function_exists("getRegistrarConfigOptions")) {
    require ROOTDIR . "/includes/registrarfunctions.php";
}
if(!function_exists("ModuleBuildParams")) {
    require ROOTDIR . "/includes/modulefunctions.php";
}
if(!function_exists("changeOrderStatus")) {
    require ROOTDIR . "/includes/orderfunctions.php";
}
$whmcs = App::self();
$result = select_query("tblorders", "", ["id" => $orderid, "status" => "Pending"]);
$data = mysql_fetch_array($result);
if(!is_array($data) || empty($data["id"])) {
    $apiresults = ["result" => "error", "message" => "Order ID not found or Status not Pending"];
} else {
    if($cancelSubscription = (bool) $whmcs->get_req_var("cancelsub")) {
        require_once ROOTDIR . "/includes/gatewayfunctions.php";
    }
    $msg = changeOrderStatus($orderid, "Cancelled", $cancelSubscription);
    if($msg == "subcancelfailed") {
        $apiresults = ["result" => "error", "message" => "Subscription Cancellation Failed - Please check the gateway log for further information"];
    } else {
        $apiresults = ["result" => "success"];
    }
}

?>