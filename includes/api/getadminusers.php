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
$roleId = (int) App::getFromRequest("roleid");
$email = App::getFromRequest("email");
$includeDisabled = (int) App::getFromRequest("include_disabled");
$admins = WHMCS\User\Admin::orderBy("firstname")->orderBy("lastname");
if($roleId) {
    $admins->where("roleid", $roleId);
}
if($email) {
    $admins->where("email", "LIKE", "%" . $email . "%");
}
if(!$includeDisabled) {
    $admins->where("disabled", 0);
}
$apiresults["count"] = 0;
foreach ($admins->get() as $admin) {
    $adminData = $admin->toArrayUsingColumnMapNames();
    foreach (["supportDepartmentIds", "receivesTicketNotifications"] as $key) {
        $adminData[$key] = explode(",", $adminData[$key]);
    }
    $apiresults["admin_users"][] = $adminData;
}
$apiresults["count"] = count($apiresults["admin_users"]);

?>