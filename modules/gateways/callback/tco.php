<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
require "../../../init.php";
$gatewayModuleName = "tco";
App::load_function("gateway");
App::load_function("invoice");
try {
    $requestHelper = new WHMCS\Module\Gateway\TCO\CallbackRequestHelper(WHMCS\Http\Message\ServerRequest::fromGlobals());
    $gatewayParams = $requestHelper->getGatewayParams();
    $callable = $requestHelper->getCallable();
    $result = call_user_func($callable, $gatewayParams);
} catch (Exception $e) {
    WHMCS\Terminus::getInstance()->doDie($e->getMessage());
}

?>