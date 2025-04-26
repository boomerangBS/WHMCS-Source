<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
require_once "init.php";
$rss = new WHMCS\Announcement\Rss();
$request = Laminas\Diactoros\ServerRequestFactory::fromGlobals();
$response = $rss->toXml($request);
(new Laminas\HttpHandlerRunner\Emitter\SapiEmitter())->emit($response);

?>