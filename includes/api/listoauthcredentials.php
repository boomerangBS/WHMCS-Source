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
$grantType = $whmcs->getFromRequest("grantType");
$sortField = $whmcs->getFromRequest("sortField");
$sortOrder = $whmcs->getFromRequest("sortOrder");
$limit = $whmcs->getFromRequest("limit");
$clients = WHMCS\ApplicationLink\Client::where("id", "!=", 0);
if($grantType) {
    $clients->where("grant_types", "LIKE", "%" . $grantType . "%");
}
if($sortField) {
    $clients->orderBy($sortField, $sortOrder);
}
if($limit) {
    $clients->limit($limit);
}
$clientsToReturn = [];
foreach ($clients->get() as $data) {
    $clientsToReturn[] = ["credentialId" => $data->id, "name" => $data->name, "description" => $data->description, "grantTypes" => implode(" ", $data->grantTypes), "scope" => $data->scope, "clientIdentifier" => $data->identifier, "clientSecret" => $data->decryptedSecret, "uuid" => $data->uuid, "serviceId" => $data->serviceId, "logoUri" => $data->logoUri, "redirectUri" => $data->redirectUri, "rsaKeyPairId" => $data->rsa_key_pair_id, "createdAt" => $data->created_at->format("jS F Y g:i:sa"), "updatedAt" => $data->updated_at->format("jS F Y g:i:sa")];
}
$apiresults = ["result" => "success", "clients" => $clientsToReturn];

?>