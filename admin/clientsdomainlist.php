<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
define("ADMINAREA", true);
require "../init.php";
$aInt = new WHMCS\Admin("List Domains");
App::redirectToRoutePath("admin-domains-index");

?>