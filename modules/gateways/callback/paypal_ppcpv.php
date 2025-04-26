<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
require "../../../init.php";
$responseHeader = WHMCS\Module\Gateway\paypal_ppcpv\Handler\WebhookResponseHandler::eventReplyHeader(200, "OK");
try {
    $request = WHMCS\Http\Message\ServerRequest::fromGlobals();
    $webHookRequest = WHMCS\Module\Gateway\paypal_ppcpv\API\WebhookEventRequest::factory($request->getHeaders(), $request->getBody()->getContents());
    $responseHeader = WHMCS\Module\Gateway\paypal_ppcpv\Handler\AbstractHandler::factory("paypal_ppcpv_webhook_response_handler", WHMCS\Module\Gateway\paypal_ppcpv\PayPalCommerce::loadModule(), WHMCS\Module\Gateway\paypal_ppcpv\SystemConfiguration::singleton(DI::make("app")), WHMCS\Module\Gateway\paypal_ppcpv\ModuleConfiguration::fromPersistance())->setACDCLog(WHMCS\Module\Gateway\paypal_acdc\Logger::factory(WHMCS\Module\Gateway\paypal_acdc\ModuleConfiguration::fromPersistance(), WHMCS\Module\Gateway\paypal_acdc\Core::loadModule()))->handle($webHookRequest);
} catch (Exception $e) {
    $responseHeader = WHMCS\Module\Gateway\paypal_ppcpv\Handler\WebhookResponseHandler::eventReplyHeader(406, "Not Acceptable");
} finally {
    header("HTTP/1.0 " . $responseHeader);
}

?>