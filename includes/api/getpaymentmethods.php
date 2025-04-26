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
if(!function_exists("getGatewaysArray")) {
    require ROOTDIR . "/includes/gatewayfunctions.php";
}
$paymentmethods = getGatewaysArray();
$apiresults = ["result" => "success", "totalresults" => count($paymentmethods)];
foreach ($paymentmethods as $module => $name) {
    $apiresults["paymentmethods"]["paymentmethod"][] = ["module" => $module, "displayname" => $name];
}
$responsetype = "xml";

?>