<?php

define("ADMINAREA", true);
require "../init.php";
$aInt = new WHMCS\Admin("Manage API Credentials", false);
$aInt->title = AdminLang::trans("setup.apicredentials");
$aInt->sidebar = "config";
$aInt->icon = "admins";
$aInt->helplink = "API_Authentication_Credentials";
$aInt->requireAuthConfirmation();
$controller = new WHMCS\Authentication\DeviceConfigurationController();
$request = WHMCS\Http\Message\ServerRequest::fromGlobals();
$action = $request->get("action");
$response = "";
if($action === "generate") {
    check_token("WHMCS.admin.default");
    $response = $controller->generate($request);
} elseif($action === "delete") {
    check_token("WHMCS.admin.default");
    $response = $controller->delete($request);
} elseif($action === "savefield") {
    check_token("WHMCS.admin.default");
    $response = $controller->updateFields($request);
} elseif($action === "getDevices") {
    $response = $controller->getDevices($request);
} else {
    $request = $request->withAttribute("aInt", $aInt);
    $response = $controller->index($request);
}
(new Laminas\HttpHandlerRunner\Emitter\SapiEmitter())->emit($response);
exit;

?>