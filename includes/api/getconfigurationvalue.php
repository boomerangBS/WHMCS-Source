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
$setting = App::get_req_var("setting");
if(!$setting) {
    $apiresults = ["result" => "error", "message" => "Parameter setting is required"];
} else {
    $currentValue = WHMCS\Config\Setting::find($setting);
    if(is_null($currentValue)) {
        $apiresults = ["result" => "error", "message" => "Invalid name for parameter setting"];
    } else {
        $apiresults = ["result" => "success", "setting" => $setting, "value" => $currentValue->value];
    }
}

?>