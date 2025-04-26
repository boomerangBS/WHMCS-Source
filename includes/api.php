<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
require_once dirname(__DIR__) . "/init.php";
$request = WHMCS\Api\ApplicationSupport\Http\ServerRequest::fromGlobals();
$responseData = [];
$statusCode = 200;
try {
    $response = DI::make("Frontend\\Dispatcher")->dispatch($request);
} catch (Exception $e) {
    $responseData = ["result" => "error", "message" => $e->getMessage()];
    if($e->getCode() === 0 && $e->getCode() === 200) {
        $statusCode = $e->getCode();
    }
} finally {
    if(!$response instanceof Psr\Http\Message\ResponseInterface) {
        $response = WHMCS\Api\ApplicationSupport\Http\ResponseFactory::factory($request, $responseData, $statusCode);
    }
}

?>