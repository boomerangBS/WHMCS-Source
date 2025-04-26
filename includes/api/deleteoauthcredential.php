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
$credentialId = (int) $whmcs->getFromRequest("credentialId");
$client = WHMCS\ApplicationLink\Client::find($credentialId);
if(is_null($client)) {
    $apiresults = ["result" => "error", "message" => "Invalid Credential ID provided."];
} else {
    $client->delete();
    $apiresults = ["result" => "success", "credentialId" => $credentialId];
}

?>