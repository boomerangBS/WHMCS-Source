<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
define("ADMINAREA", true);
require "../init.php";
$aInt = new WHMCS\Admin("Configure General Settings");
$aInt->title = "URI Path Management";
$aInt->sidebar = "config";
$aInt->icon = "autosettings";
$aInt->helplink = "URI Path Management";
$response = "";
$action = App::get_req_var("action");
$request = WHMCS\Http\Message\ServerRequest::fromGlobals();
$action = $request->get("action", "view");
$configurationController = new WHMCS\Admin\Setup\General\UriManagement\ConfigurationController();
if($action == "toggle") {
    check_token("WHMCS.admin.default");
    $response = $configurationController->updateUriManagementSetting($request);
} elseif($action == "updateUriPathMode") {
    check_token("WHMCS.admin.default");
    $response = $configurationController->setEnvironmentMode($request);
} elseif($action == "synchronize") {
    check_token("WHMCS.admin.default");
    $response = $configurationController->synchronizeRules($request);
} elseif($action == "remoteDetectEnvironmentModeAndSet") {
    check_token("WHMCS.admin.default");
    $request = $request->withAttribute("setDetected", true);
    $response = $configurationController->remoteDetectEnvironmentMode($request);
} elseif($action == "applyBestConfiguration") {
    check_token("WHMCS.admin.default");
    $response = $configurationController->applyBestConfiguration($request);
} elseif($action == "modal-view") {
    $request = $request->withAttribute("modal-view", true);
    $response = $configurationController->view($request);
} else {
    $response = $configurationController->view($request);
}
(new Laminas\HttpHandlerRunner\Emitter\SapiEmitter())->emit($response);
exit;

?>