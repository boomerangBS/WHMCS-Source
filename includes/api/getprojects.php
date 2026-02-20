<?php

if(!defined("WHMCS")) {
    exit("This file cannot be accessed directly");
}
if(empty($limitstart)) {
    $limitstart = 0;
}
if(empty($limitnum)) {
    $limitnum = 25;
}
$query = WHMCS\Database\Capsule::table("mod_project");
if(!empty($userid)) {
    $query = $query->where("userid", "=", $userid);
}
if(!empty($title)) {
    $query = $query->where("title", "like", $title);
}
if(!empty($ticketids)) {
    $query = $query->where("ticketids", "like", $ticketids);
}
if(!empty($invoiceids)) {
    $query = $query->where("invoiceids", "like", $invoiceids);
}
if(!empty($notes)) {
    $query = $query->where("notes", "like", $notes);
}
if(isset($_REQUEST["adminid"])) {
    $query = $query->where("adminid", "=", $_REQUEST["adminid"]);
}
if(!empty($status)) {
    $query = $query->where("status", "like", $status);
}
if(!empty($created)) {
    $query = $query->where("created", "like", $created);
}
if(!empty($duedate)) {
    $query = $query->where("duedate", "like", $duedate);
}
if(isset($completed)) {
    $query = $query->where("completed", "like", $completed);
}
if(!empty($lastmodified)) {
    $query = $query->where("lastmodified", "like", $lastmodified);
}
$totalresults = $query->count();
$result = $query->orderBy("id", "ASC")->skip($limitstart)->limit($limitnum)->get()->all();
$apiresults = ["result" => "success", "totalresults" => $totalresults, "startnumber" => $limitstart, "numreturned" => count($result), "projects" => []];
foreach ($result as $row) {
    $apiresults["projects"][] = (array) $row;
}
$responsetype = "xml";

?>