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
$contactid = App::getFromRequest("contactid");
try {
    $contact = WHMCS\User\Client\Contact::findOrFail($contactid);
} catch (Exception $e) {
    $apiresults = ["result" => "error", "message" => "Contact ID Not Found"];
    return NULL;
}
$client = $contact->client;
$legacyClient = new WHMCS\Client($client);
$legacyClient->deleteContact($contactid);
$apiresults = ["result" => "success", "message" => $contactid];

?>