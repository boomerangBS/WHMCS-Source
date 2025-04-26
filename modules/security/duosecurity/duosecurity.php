<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
function duosecurity_config()
{
    $twofa = new WHMCS\TwoFactorAuthentication();
    $integrationKey = decrypt($twofa->getModuleSetting("duosecurity", "integrationKey"));
    $secretKey = decrypt($twofa->getModuleSetting("duosecurity", "secretKey"));
    $apiHostname = $twofa->getModuleSetting("duosecurity", "apiHostname");
    $extraDescription = "";
    if(!$integrationKey && !$secretKey && !$apiHostname) {
        $extraDescription .= "<div class=\"alert alert-success\" style=\"margin:10px 0;padding:8px 15px;\">New to Duo Security? <a href=\"http://go.whmcs.com/918/duo-security-signup\" target=\"_blank\" class=\"alert-link\">Click here to create an account</a></div>";
    }
    return ["FriendlyName" => ["Type" => "System", "Value" => "Duo Security"], "ShortDescription" => ["Type" => "System", "Value" => "Get codes via Duo Push, SMS or Phone Callback."], "Description" => ["Type" => "System", "Value" => "Duo Security enables your users to secure their logins using their smartphones. Authentication options include push notifications, passcodes, text messages and/or phone calls." . $extraDescription], "integrationKey" => ["FriendlyName" => "Client ID", "Type" => "password", "Size" => "25"], "secretKey" => ["FriendlyName" => "Client Secret", "Type" => "password", "Size" => "45"], "apiHostname" => ["FriendlyName" => "API Hostname", "Type" => "text", "Size" => "45"]];
}
function duosecurity_activate(array $params)
{
}
function duosecurity_activateverify(array $params)
{
    return ["msg" => "You will be asked to configure your Duo Security Two-Factor Authentication the next time you login."];
}
function duosecurity_challenge($params)
{
    $whmcs = App::self();
    $username = $params["user_info"]["username"];
    $email = $params["user_info"]["email"];
    $uid = $username . ":" . $email . ":" . $whmcs->get_license_key();
    $inAdmin = defined("ADMINAREA");
    $integrationkey = !empty($params["settings"]["integrationKey"]) ? decrypt($params["settings"]["integrationKey"]) : "";
    $secretkey = !empty($params["settings"]["secretKey"]) ? decrypt($params["settings"]["secretKey"]) : "";
    $apihostname = WHMCS\Input\Sanitize::escapeSingleQuotedString($params["settings"]["apiHostname"]);
    if($inAdmin) {
        $route = WHMCS\Utility\Environment\WebHelper::getAdminFQRootUrl() . "/dologin.php";
    } else {
        $route = fqdnRoutePath("login-two-factor-challenge-verify");
    }
    $output = "Duo® Security 2FA is unavailable at this time.";
    try {
        $duo_client = new Duo\DuoUniversal\Client($integrationkey, $secretkey, $apihostname, $route);
    } catch (Duo\DuoUniversal\DuoException $e) {
        logActivity("Duo® Security configuration error: " . $e->getMessage());
        return $output;
    }
    try {
        $duo_client->healthCheck();
    } catch (Duo\DuoUniversal\DuoException $e) {
        $msg = WHMCS\Input\Sanitize::encode($e->getMessage());
        logActivity("Duo® Security service availability: " . $msg);
        $output = "Duo® Security is experiencing a service disruption. <br>Log in with your backup code";
        return $output;
    }
    $state = $duo_client->generateState();
    $auth_url = $duo_client->createAuthUrl($uid, $state);
    WHMCS\Session::set("duosecurity.state", $state);
    $response = new Symfony\Component\HttpFoundation\RedirectResponse($auth_url);
    $response->send();
    WHMCS\Terminus::getInstance()->doExit();
}
function duosecurity_verify($params)
{
    $state = App::getFromRequest("state") ?? "";
    $saved_state = WHMCS\Session::get("duosecurity.state", "");
    if($state == "" || $saved_state == "") {
        return false;
    }
    return hash_equals($state, $saved_state);
}
function duosecurity_get_fields(array $params)
{
    return [];
}
function duosecurity_get_error()
{
    return App::isInRequest("error") ? App::getFromRequest("error") : NULL;
}

?>