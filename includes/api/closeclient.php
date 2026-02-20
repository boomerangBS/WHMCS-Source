<?php

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