<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
require_once dirname(dirname(__DIR__)) . "/init.php";
error_reporting(32759);
$request = WHMCS\Http\Message\ServerRequest::fromGlobals();
$response = DI::make("Frontend\\Dispatcher")->dispatch($request);
(new Laminas\HttpHandlerRunner\Emitter\SapiEmitter())->emit($response);
exit;

?>