<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
require "../../../init.php";
App::load_function("gateway");
App::load_function("invoice");
$gatewayParams = ["paymentmethod" => "stripe_ach"];
$logTransactionResult = "";
$data = $passedParams = [];
$payload = @file_get_contents("php://input");
try {
    $webhookHandler = WHMCS\Module\Gateway\StripeAch\WebhookHandler::factory()->validateEventForModule()->handleEvent();
    $data = $webhookHandler->getData();
    $logTransactionResult = $webhookHandler->getLogType();
    $passedParams = $webhookHandler->getPassedParams();
} catch (WHMCS\Payment\Exception\InvalidModuleException $e) {
    $data = ["error" => $e->getMessage()];
    $logTransactionResult = "Module Not Active";
} catch (Stripe\Exception\SignatureVerificationException $e) {
    $data = ["payload" => $payload, "error" => $e->getMessage()];
    $logTransactionResult = "Invalid Access Attempt";
} catch (WHMCS\Exception\Module\NotServicable $e) {
    WHMCS\Terminus::getInstance()->doExit();
} catch (Exception $e) {
    $data = ["payload" => $payload, "error" => $e->getMessage()];
    $logTransactionResult = "Invalid Response";
    http_response_code(400);
}
logTransaction($gatewayParams["paymentmethod"], $data, $logTransactionResult, $passedParams);
WHMCS\Terminus::getInstance()->doExit();

?>