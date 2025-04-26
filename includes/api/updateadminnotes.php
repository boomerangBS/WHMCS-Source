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
$admin = WHMCS\User\Admin::find((int) WHMCS\Session::get("adminid"));
if(is_null($admin)) {
    $apiresults = ["result" => "error", "message" => "You must be authenticated as an admin user to perform this action"];
} else {
    $admin->notes = $notes;
    $admin->save();
    $apiresults = ["result" => "success"];
}

?>