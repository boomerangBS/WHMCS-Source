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
try {
    $client = WHMCS\User\Client::findOrFail($clientid);
    $client->closeClient();
    $apiresults = ["result" => "success", "clientid" => $client->id];
} catch (Illuminate\Database\Eloquent\ModelNotFoundException $e) {
    $apiresults = ["result" => "error", "message" => "Client ID Not Found"];
} catch (Exception $e) {
    $apiresults = ["result" => "error", "message" => "An unexpected error occurred: " . $e->getMessage()];
}

?>