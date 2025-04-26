<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
define("CLIENTAREA", true);
require "init.php";
$userId = NULL;
if(Auth::user()) {
    $client = Auth::client();
    if($client) {
        $userId = $client->id;
    }
    Auth::logout();
}
if(App::getFromRequest("returntoadmin") && WHMCS\User\Admin::getAuthenticatedUser()) {
    if($userId) {
        App::redirect(App::get_admin_folder_name() . "/clientssummary.php", ["userid" => $userId]);
    }
    App::redirect(App::get_admin_folder_name());
}
App::redirect("index.php");

?>