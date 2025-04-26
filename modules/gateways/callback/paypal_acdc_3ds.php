<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
require "../../../init.php";
if(isset($_POST["3dsc"])) {
    if(!is_string($_POST["3dsc"]) || strlen($_POST["3dsc"]) == 0) {
        header("HTTP/1.0 400 Bad Request");
        exit;
    }
    $hashedOrderIdentifier = App::sanitize("a-z", $_POST["3dsc"]);
    $handler = WHMCS\Module\Gateway\paypal_acdc\Handler\AbstractHandler::factory("paypal_acdc_three_d_secure", WHMCS\Module\Gateway\paypal_acdc\Core::loadModule(), WHMCS\Module\Gateway\paypal_ppcpv\SystemConfiguration::singleton(DI::make("app")), WHMCS\Module\Gateway\paypal_acdc\ModuleConfiguration::fromPersistance());
    try {
        list($captureResult, $invoice) = $handler->handle($hashedOrderIdentifier);
        if(!is_null($captureResult) && $captureResult->isComplete()) {
            App::fqRedirect($handler->invoiceCaptureSuccessUrl($invoice->id));
        }
        App::fqRedirect($handler->invoiceCaptureFailureUrl($invoice->id));
    } catch (Exception $e) {
        App::redirectToRoutePath("clientarea-home");
    }
}
$hash = App::getFromRequest("hash");
if(!is_string($hash) || strlen($hash) == 0) {
    header("HTTP/1.0 400 Bad Request");
    exit;
}
echo WHMCS\Module\Gateway\paypal_acdc\Handler\ThreeDSecure::frameBreakout(App::getPhpSelf(), $hash);

?>