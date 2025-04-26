<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
require "init.php";
$id = (int) $whmcs->get_req_var("id");
$url = get_query_val("tbllinks", "link", ["id" => $id]);
if($url) {
    update_query("tbllinks", ["clicks" => "+1"], ["id" => $id]);
    WHMCS\Cookie::set("LinkID", $id, "3m");
    run_hook("LinkTracker", ["linkid" => $id]);
    header("Location: " . WHMCS\Input\Sanitize::decode($url));
    exit;
}
redir("", "index.php");

?>