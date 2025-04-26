<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
require_once __DIR__ . DIRECTORY_SEPARATOR . "bootstrap.php";
$server = DI::make("oauth2_server");
$server->setConfig("issuer", WHMCS\ApplicationLink\Server\Server::getIssuer());
$server->handleUserInfoRequest($request, $response);
Log::debug("oauth/userinfo", ["request" => ["headers" => $request->server->getHeaders(), "request" => $request->request->all(), "query" => $request->query->all()], "response" => ["body" => $response->getContent()]]);
$response->send();

?>