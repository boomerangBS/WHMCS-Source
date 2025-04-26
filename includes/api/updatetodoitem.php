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
$id = get_query_val("tbltodolist", "id", ["id" => $itemid]);
if(!$itemid) {
    $apiresults = ["result" => "error", "message" => "TODO Item ID Not Found"];
} else {
    $adminid = get_query_val("tbladmins", "id", ["id" => $adminid]);
    if(!$adminid) {
        $apiresults = ["result" => "error", "message" => "Admin ID Not Found"];
    } else {
        $todoarray = [];
        if($date) {
            $todoarray["date"] = toMySQLDate($date);
        }
        if($title) {
            $todoarray["title"] = $title;
        }
        if($description) {
            $todoarray["description"] = $description;
        }
        if($adminid) {
            $todoarray["admin"] = $adminid;
        }
        if($status) {
            $todoarray["status"] = $status;
        }
        if($duedate) {
            $todoarray["duedate"] = toMySQLDate($duedate);
        }
        update_query("tbltodolist", $todoarray, ["id" => $itemid]);
        $apiresults = ["result" => "success", "itemid" => $itemid];
    }
}

?>