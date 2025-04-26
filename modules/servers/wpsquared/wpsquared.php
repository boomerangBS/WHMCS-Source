<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
if(!defined("WHMCS")) {
    exit("This file cannot be accessed directly");
}
$licensing = DI::make("license");
if(defined("WPSQUAREDCONFPACKAGEADDONLICENSE")) {
    exit("License Hacking Attempt Detected");
}
define("WPSQUAREDCONFPACKAGEADDONLICENSE", $licensing->isActiveAddon("Configurable Package Addon"));
include_once __DIR__ . DIRECTORY_SEPARATOR . "lib" . DIRECTORY_SEPARATOR . "WpSquared" . DIRECTORY_SEPARATOR . "ApplicationLink" . DIRECTORY_SEPARATOR . "Server.php";
function wpsquared_MetaData()
{
    return ["DisplayName" => "WP Squared", "APIVersion" => "1.1", "DefaultNonSSLPort" => "2086", "DefaultSSLPort" => "2087", "ServiceSingleSignOnLabel" => "Log in to WP Squared", "AdminSingleSignOnLabel" => "Log in to WHM", "ApplicationLinkDescription" => "Provides customers with links that utilise Single Sign-On technology to automatically transfer and log your customers into the WHMCS billing &amp; support portal from within the WP Squared user interface.", "ListAccountsUniqueIdentifierDisplayName" => "Domain", "ListAccountsUniqueIdentifierField" => "domain", "ListAccountsProductField" => "configoption1"];
}
function wpsquared_ConfigOptions(array $params)
{
    $resellerSimpleMode = $params["producttype"] == "reselleraccount";
    return ["WHM Package Name" => ["Type" => "text", "Size" => "25", "Loader" => "wpsquared_ListPackages", "SimpleMode" => true], "Max WordPress Instances" => ["Type" => "text", "Size" => "5"], "Web Space Quota" => ["Type" => "text", "Size" => "5", "Description" => "MB"], "Bandwidth Limit" => ["Type" => "text", "Size" => "5", "Description" => "MB"], "Dedicated IP" => ["Type" => "yesno"], "Shell Access" => ["Type" => "yesno", "Description" => "Check to grant access"], "Max Parked Domains" => ["Type" => "text", "Size" => "5"]];
}
function wpsquared_costrrpl($val)
{
    $val = str_replace("MB", "", $val);
    $val = str_replace("Accounts", "", $val);
    $val = trim($val);
    if($val == "Yes") {
        $val = true;
    } elseif($val == "No") {
        $val = false;
    } elseif($val == "Unlimited") {
        $val = "unlimited";
    }
    return $val;
}
function wpsquared_CreateAccount($params)
{
    $languageco = "";
    if(WPSQUAREDCONFPACKAGEADDONLICENSE) {
        if(isset($params["configoptions"]["Disk Space"])) {
            $params["configoption3"] = wpsquared_costrrpl($params["configoptions"]["Disk Space"]);
        }
        if(isset($params["configoptions"]["Bandwidth"])) {
            $params["configoption4"] = wpsquared_costrrpl($params["configoptions"]["Bandwidth"]);
        }
        if(isset($params["configoptions"]["Parked Domains"])) {
            $params["configoption7"] = wpsquared_costrrpl($params["configoptions"]["Parked Domains"]);
        }
        if(isset($params["configoptions"]["WordPress Instances"])) {
            $params["configoption2"] = wpsquared_costrrpl($params["configoptions"]["WordPress Instances"]);
        }
        if(isset($params["configoptions"]["Dedicated IP"])) {
            $params["configoption5"] = wpsquared_costrrpl($params["configoptions"]["Dedicated IP"]);
        }
        if(isset($params["configoptions"]["Shell Access"])) {
            $params["configoption6"] = wpsquared_costrrpl($params["configoptions"]["Shell Access"]);
        }
        if(isset($params["configoptions"]["Package Name"])) {
            $params["configoption1"] = $params["configoptions"]["Package Name"];
        }
        if(isset($params["configoptions"]["Language"])) {
            $languageco = $params["configoptions"]["Language"];
        }
    }
    $dedicatedip = (bool) $params["configoption5"];
    $shellaccess = (bool) $params["configoption6"];
    try {
        $packages = wpsquared_ListPackages($params, false);
    } catch (Exception $e) {
        return $e->getMessage();
    }
    $postfields = [];
    $postfields["username"] = $params["username"];
    $postfields["password"] = $params["password"];
    $postfields["domain"] = $params["domain"];
    $postfields["savepkg"] = 0;
    $packageRequired = true;
    if(isset($params["configoption3"]) && $params["configoption3"] != "") {
        $postfields["quota"] = $params["configoption3"];
        $packageRequired = false;
    }
    if(isset($params["configoption4"]) && $params["configoption4"] != "") {
        $postfields["bwlimit"] = $params["configoption4"];
        $packageRequired = false;
    }
    if($params["configoption1"] == "") {
        $packageRequired = false;
    }
    if($dedicatedip) {
        $postfields["ip"] = $dedicatedip;
    }
    if($shellaccess) {
        $postfields["hasshell"] = $shellaccess;
    }
    $postfields["contactemail"] = $params["clientsdetails"]["email"];
    if(isset($params["configoption7"]) && $params["configoption7"] != "") {
        $postfields["maxpark"] = $params["configoption7"];
    }
    if(isset($params["configoption2"]) && $params["configoption2"] != "") {
        $postfields["maxaddon"] = $params["configoption2"];
    }
    if(isset($languageco) && $languageco != "") {
        $postfields["language"] = $languageco;
    }
    try {
        $postfields["plan"] = wpsquared_ConfirmPackageName($params["configoption1"], $params["serverusername"], $packages);
    } catch (WHMCS\Exception\Module\NotServicable $e) {
        if($packageRequired) {
            return $e->getMessage();
        }
        $postfields["plan"] = $params["configoption1"];
    } catch (Exception $e) {
        return $e->getMessage();
    }
    $postfields["api.version"] = 1;
    $postfields["reseller"] = 0;
    $output = wpsquared_jsonRequest($params, "/json-api/createacct", $postfields);
    if(!is_array($output)) {
        return $output;
    }
    if(array_key_exists("metadata", $output) && $output["metadata"]["result"] == "0") {
        $error = $output["metadata"]["reason"];
        if(!$error) {
            $error = "An unknown error occurred";
        }
        return $error;
    }
    if($dedicatedip) {
        $newaccountip = $output["data"]["ip"];
        $params["model"]->serviceProperties->save(["dedicatedip" => $newaccountip]);
    }
    return "success";
}
function wpsquared_SuspendAccount($params)
{
    if(!$params["username"]) {
        return "Cannot perform action without the account's username";
    }
    try {
        $urlEncodedUsername = urlencode($params["username"]);
        $urlEncodedSuspendReason = urlencode($params["suspendreason"]);
        $postVars = "api.version=1&user=" . $urlEncodedUsername . "&reason=" . $urlEncodedSuspendReason;
        $output = wpsquared_jsonRequest($params, "/json-api/suspendacct", $postVars);
    } catch (Throwable $e) {
        return $e->getMessage();
    }
    if(!is_array($output)) {
        return $output;
    }
    $metadata = isset($output["metadata"]) ? $output["metadata"] : [];
    $resultCode = isset($metadata["result"]) ? $metadata["result"] : 0;
    if($resultCode == "1") {
        return "success";
    }
    return isset($metadata["reason"]) ? $metadata["reason"] : "An unknown error occurred";
}
function wpsquared_UnsuspendAccount($params)
{
    if(!$params["username"]) {
        return "Cannot perform action without the account's username";
    }
    try {
        $urlEncodedUsername = urlencode($params["username"]);
        $postVars = "api.version=1&user=" . $urlEncodedUsername;
        $output = wpsquared_jsonRequest($params, "/json-api/unsuspendacct", $postVars);
    } catch (Throwable $e) {
        return $e->getMessage();
    }
    if(!is_array($output)) {
        return $output;
    }
    $metadata = isset($output["metadata"]) ? $output["metadata"] : [];
    $resultCode = isset($metadata["result"]) ? $metadata["result"] : 0;
    if($resultCode == "1") {
        return "success";
    }
    return isset($metadata["reason"]) ? $metadata["reason"] : "An unknown error occurred";
}
function wpsquared_TerminateAccount($params)
{
    if(!$params["username"]) {
        return "Cannot perform action without the account's username";
    }
    try {
        $request = ["user" => $params["username"], "keepdns" => 0];
        if(array_key_exists("keepZone", $params)) {
            $request["keepdns"] = $params["keepZone"];
        }
        $output = wpsquared_jsonRequest($params, "/json-api/removeacct", $request);
        if(!is_array($output)) {
            return $output;
        }
        if(!$output["result"][0]["status"]) {
            $error = $output["result"][0]["statusmsg"];
            if(!$error) {
                $error = "An unknown error occurred";
            }
            return $error;
        }
    } catch (Throwable $e) {
        return $e->getMessage();
    }
    return "success";
}
function wpsquared_ChangePassword($params)
{
    $postVars = "user=" . $params["username"] . "&pass=" . urlencode($params["password"]);
    try {
        $output = wpsquared_jsonRequest($params, "/json-api/passwd", $postVars);
    } catch (Throwable $e) {
        return $e->getMessage();
    }
    if(!is_array($output)) {
        return $output;
    }
    if(!$output["passwd"][0]["status"]) {
        $error = $output["passwd"][0]["statusmsg"];
        if(!$error) {
            $error = "An unknown error occurred";
        }
        return $error;
    }
    return "success";
}
function wpsquared_ChangePackage($params)
{
    if(array_key_exists("Package Name", $params["configoptions"])) {
        $params["configoption1"] = $params["configoptions"]["Package Name"];
    }
    try {
        $packages = wpsquared_ListPackages($params, false);
        if($params["configoption1"] != "Custom") {
            try {
                $plan = wpsquared_ConfirmPackageName($params["configoption1"], $params["serverusername"], $packages);
            } catch (Exception $e) {
                return $e->getMessage();
            }
            $urlEncodedPlan = urlencode($plan);
            $postVars = "user=" . $params["username"] . "&pkg=" . $urlEncodedPlan;
            $output = wpsquared_jsonRequest($params, "/json-api/changepackage", $postVars);
            if(!is_array($output)) {
                return $output;
            }
            if(!$output["result"][0]["status"]) {
                $error = $output["result"][0]["statusmsg"];
                if(!$error) {
                    $error = "An unknown error occurred";
                }
                return $error;
            }
        }
        if(WPSQUAREDCONFPACKAGEADDONLICENSE && count($params["configoptions"])) {
            if(isset($params["configoptions"]["Disk Space"])) {
                $params["configoption3"] = wpsquared_costrrpl($params["configoptions"]["Disk Space"]);
                $postVars = "api.version=1&user=" . urlencode($params["username"]) . "&quota=" . urlencode($params["configoption3"]);
                $output = wpsquared_jsonRequest($params, "/json-api/editquota", $postVars);
            }
            if(isset($params["configoptions"]["Bandwidth"])) {
                $params["configoption4"] = wpsquared_costrrpl($params["configoptions"]["Bandwidth"]);
                $urlEncodedUsername = urlencode($params["username"]);
                $urlEncodedConfigOption = urlencode($params["configoption4"]);
                $postVars = "api.version=1&user=" . $urlEncodedUsername . "&bwlimit=" . $urlEncodedConfigOption;
                $output = wpsquared_jsonRequest($params, "/json-api/limitbw", $postVars);
            }
            $postVars = "";
            if(isset($params["configoptions"]["Parked Domains"])) {
                $params["configoption7"] = wpsquared_costrrpl($params["configoptions"]["Parked Domains"]);
                $postVars .= "MAXPARK=" . $params["configoption7"] . "&";
            }
            if(isset($params["configoptions"]["WordPress Instances"])) {
                $params["configoption2"] = wpsquared_costrrpl($params["configoptions"]["WordPress Instances"]);
                $postVars .= "MAXADDON=" . $params["configoption2"] . "&";
            }
            if(isset($params["configoptions"]["Shell Access"])) {
                $params["configoption6"] = wpsquared_costrrpl($params["configoptions"]["Shell Access"]);
                $postVars .= "shell=" . $params["configoption6"] . "&";
            }
            if($postVars) {
                $postVars = "user=" . $params["username"] . "&domain=" . $params["domain"] . "&" . $postVars;
                $output = wpsquared_jsonRequest($params, "/json-api/modifyacct", $postVars);
            }
            if(isset($params["configoptions"]["Dedicated IP"])) {
                $params["configoption5"] = wpsquared_costrrpl($params["configoptions"]["Dedicated IP"]);
                if($params["configoption5"]) {
                    $currentip = "";
                    $alreadydedi = false;
                    $postVars = "user=" . $params["username"];
                    $output = wpsquared_jsonRequest($params, "/json-api/accountsummary", $postVars);
                    $currentip = $output["acct"][0]["ip"];
                    $output = wpsquared_jsonRequest($params, "/json-api/listips", []);
                    foreach ($output["result"] as $result) {
                        if($result["ip"] == $currentip && $result["mainaddr"] != "1") {
                            $alreadydedi = true;
                        }
                    }
                    if(!$alreadydedi) {
                        foreach ($output["result"] as $result) {
                            $active = $result["active"];
                            $dedicated = $result["dedicated"];
                            $ipaddr = $result["ip"];
                            $used = $result["used"];
                            if($active && $dedicated && !$used) {
                                $postVars = "user=" . $params["username"] . "&ip=" . $ipaddr;
                                $output = wpsquared_jsonRequest($params, "/json-api/setsiteip", $postVars);
                                if($output["result"][0]["status"]) {
                                    $params["model"]->serviceProperties->save(["dedicatedip" => $ipaddr]);
                                }
                            }
                        }
                    }
                }
            }
        }
    } catch (Throwable $e) {
        return $e->getMessage();
    }
    return "success";
}
function wpsquared_UsageUpdate(array $params)
{
    $params["overrideTimeout"] = 30;
    try {
        $output = wpsquared_jsonRequest($params, "/json-api/listaccts", []);
    } catch (Exception $e) {
        return $e->getMessage();
    }
    $domainData = [];
    $addons = WHMCS\Service\Addon::whereHas("productAddon", function ($query) {
        $query->where("module", "wpsquared");
    })->with("productAddon")->where("server", "=", $params["serverid"])->whereIn("status", ["Active", "Suspended"])->get();
    if(is_array($output) && $output["acct"]) {
        foreach ($output["acct"] as $data) {
            $domain = $data["domain"];
            $diskused = $data["diskused"];
            $disklimit = $data["disklimit"];
            $diskused = str_replace("M", "", $diskused);
            $disklimit = str_replace("M", "", $disklimit);
            $domainData[$domain] = ["diskusage" => $diskused, "disklimit" => $disklimit, "lastupdate" => WHMCS\Carbon::now()->toDateTimeString()];
        }
    }
    unset($output);
    $output = wpsquared_jsonRequest($params, "/json-api/showbw", []);
    if(is_array($output) && !empty($output["bandwidth"][0]["acct"])) {
        foreach ($output["bandwidth"][0]["acct"] as $data) {
            $domain = $data["maindomain"];
            $bwused = $data["totalbytes"];
            $bwlimit = $data["limit"];
            if(!is_numeric($bwlimit)) {
                $bwlimit = 0;
            }
            $bwused = $bwused / 1048576;
            $bwlimit = $bwlimit / 1048576;
            $domainData[$domain]["bwusage"] = $bwused;
            $domainData[$domain]["bwlimit"] = $bwlimit;
        }
    }
    unset($output);
    foreach ($domainData as $domain => $data) {
        $update = WHMCS\Database\Capsule::table("tblhosting")->where("domain", "=", $domain)->where("server", "=", $params["serverid"])->update($data);
        if(!$update) {
            foreach ($addons as $hostingAddonAccount) {
                $addonDomain = $hostingAddonAccount->serviceProperties->get("domain");
                if($addonDomain == $domain) {
                    $hostingAddonAccount->serviceProperties->save($data);
                }
            }
        }
        unset($domainData[$domain]);
    }
    unset($domainData);
}
function wpsquared_req($params, $request, $notxml = false)
{
    try {
        $requestParts = explode("?", $request, 2);
        list($apiCommand, $requestString) = $requestParts;
        $data = wpsquared_curlRequest($params, $apiCommand, $requestString);
    } catch (WHMCS\Exception $e) {
        return $e->getMessage();
    }
    if($notxml) {
        $results = $data;
    } elseif(strpos($data, "Brute Force Protection")) {
        $results = "WHM has imposed a brute force protection block: contact WP Squared for assistance";
    } elseif(strpos($data, "<form action=\"/login/\" method=\"POST\">")) {
        $results = "Login Failed";
    } elseif(strpos($data, "SSL encryption is required")) {
        $results = "SSL Required for Login";
    } elseif(strpos($data, "META HTTP-EQUIV=\"refresh\" CONTENT=") && !$usessl) {
        $results = "You must enable SSL mode";
    } else {
        if(substr($data, 0, 1) != "<") {
            $data = substr($data, strpos($data, "<"));
        }
        $results = XMLtoARRAY($data);
        if($results["CPANELRESULT"]["DATA"]["REASON"] == "Access denied") {
            $results = "Login Failed";
        }
    }
    unset($data);
    return $results;
}
function wpsquared_curlRequest($params, $apiCommand, $postVars, $stringsToMask = [])
{
    $whmIP = $params["serverip"];
    $whmHostname = $params["serverhostname"];
    $whmUsername = $params["serverusername"];
    $whmPassword = $params["serverpassword"];
    $whmHttpPrefix = $params["serverhttpprefix"];
    $whmPort = $params["serverport"];
    $whmAccessHash = preg_replace("'(\r|\n)'", "", $params["serveraccesshash"]);
    $whmSSL = $params["serversecure"] ? true : false;
    $curlTimeout = array_key_exists("overrideTimeout", $params) ? $params["overrideTimeout"] : 400;
    if(!$whmIP && !$whmHostname) {
        throw new WHMCS\Exception\Module\InvalidConfiguration("You must provide either an IP address or hostname for the server");
    }
    if(!$whmUsername) {
        throw new WHMCS\Exception\Module\InvalidConfiguration("The WHM username is missing for the selected server");
    }
    if($whmAccessHash) {
        $authStr = "WHM " . $whmUsername . ":" . $whmAccessHash;
    } elseif($whmPassword) {
        $authStr = "Basic " . base64_encode($whmUsername . ":" . $whmPassword);
    } else {
        throw new WHMCS\Exception\Module\InvalidConfiguration("You must provide either an API token (recommended) or password for WHM for the selected server");
    }
    if(substr($apiCommand, 0, 1) == "/") {
        $apiCommand = substr($apiCommand, 1);
    }
    $url = sprintf("%s://%s:%s/%s", $whmHttpPrefix, wpsquared__determineRequestAddress($whmIP, $whmHostname), $whmPort, $apiCommand);
    if(is_array($postVars)) {
        $requestString = build_query_string($postVars);
    } elseif(is_string($postVars)) {
        $requestString = $postVars;
    } else {
        $requestString = "";
    }
    $curlOptions = ["CURLOPT_HTTPHEADER" => ["Authorization: " . $authStr], "CURLOPT_TIMEOUT" => $curlTimeout];
    $ch = curlCall($url, $requestString, $curlOptions, true);
    $data = curl_exec($ch);
    if(curl_errno($ch)) {
        throw new WHMCS\Exception\Module\NotServicable("Connection Error: " . curl_error($ch) . "(" . curl_errno($ch) . ")");
    }
    if(strpos($data, "META HTTP-EQUIV=\"refresh\" CONTENT=") && !$whmSSL) {
        throw new WHMCS\Exception\Module\NotServicable("Enable SSL mode for this server and try again.");
    }
    if(!$data) {
        throw new WHMCS\Exception\Module\NotServicable("No response received. Check your connection settings.");
    }
    curl_close($ch);
    $action = str_replace(["/xml-api/", "/json-api/"], "", $apiCommand);
    logModuleCall("wpsquared", $action, $requestString, $data, "", $stringsToMask);
    return $data;
}
function wpsquared_jsonRequest($params, $apiCommand, $postVars, $stringsToMask = [])
{
    $data = wpsquared_curlrequest($params, $apiCommand, $postVars, $stringsToMask);
    if($data) {
        $decodedData = json_decode($data, true);
        if(is_null($decodedData) && json_last_error() !== JSON_ERROR_NONE) {
            throw new WHMCS\Exception\Module\NotServicable($data);
        }
        if(isset($decodedData["cpanelresult"]["error"])) {
            throw new WHMCS\Exception\Module\GeneralError($decodedData["cpanelresult"]["error"]);
        }
        if(isset($decodedData["statusmsg"]) && $decodedData["statusmsg"] === "Permission Denied") {
            throw new WHMCS\Exception\Module\GeneralError($decodedData["statusmsg"]);
        }
        if(isset($decodedData["error"])) {
            throw new WHMCS\Exception\Module\GeneralError($decodedData["error"]);
        }
        return $decodedData;
    }
    throw new WHMCS\Exception\Module\NotServicable("No Response from WHM API");
}
function wpsquared_ClientArea($params)
{
    return ["overrideDisplayTitle" => ucfirst($params["domain"]), "tabOverviewReplacementTemplate" => "overview.tpl", "tabOverviewModuleOutputTemplate" => "loginbuttons.tpl"];
}
function wpsquared_TestConnection($params)
{
    try {
        wpsquared__assertValidProfile($params);
        $response = wpsquared_jsonrequest($params, "/json-api/version", []);
        if(is_array($response) && array_key_exists("version", $response)) {
            return ["success" => true];
        }
        return ["error" => $response];
    } catch (Throwable $e) {
        return ["error" => $e->getMessage()];
    }
}
function wpsquared_SingleSignOn($params, $user, $service, $app = "")
{
    if(!$user) {
        return "Username is required for login.";
    }
    $vars = ["api.version" => "1", "user" => $user, "service" => $service];
    if($app) {
        $vars["app"] = $app;
    }
    try {
        $response = wpsquared_jsonrequest($params, "/json-api/create_user_session", $vars);
        $resultCode = isset($response["metadata"]["result"]) ? $response["metadata"]["result"] : 0;
        if($resultCode == "1") {
            $redirURL = $response["data"]["url"];
            if(!$params["serversecure"]) {
                $secureParts = ["https:", ":2087", ":2083", ":2096"];
                $insecureParts = ["http:", ":2086", ":2082", ":2095"];
                $redirURL = str_replace($secureParts, $insecureParts, $redirURL);
            }
            return ["success" => true, "redirectTo" => $redirURL];
        }
        if(isset($response["cpanelresult"]["data"]["reason"])) {
            return ["success" => false, "errorMsg" => "WP Squared API Response: " . $response["cpanelresult"]["data"]["reason"]];
        }
        if(isset($response["metadata"]["reason"])) {
            return ["success" => false, "errorMsg" => "WP Squared API Response: " . $response["metadata"]["reason"]];
        }
    } catch (WHMCS\Exception\Module\InvalidConfiguration $e) {
        return ["success" => false, "errorMsg" => "WP Squared API Configuration Problem: " . $e->getMessage()];
    } catch (WHMCS\Exception\Module\NotServicable $e) {
        return ["success" => false, "errorMsg" => "WP Squared API Unreachable: " . $e->getMessage()];
    } catch (WHMCS\Exception $e) {
    }
    return ["success" => false];
}
function wpsquared_ServiceSingleSignOn($params)
{
    $user = $params["username"];
    $app = App::get_req_var("app");
    $service = "cpaneld";
    return wpsquared_singlesignon($params, $user, $service, $app);
}
function wpsquared_AdminSingleSignOn($params)
{
    $user = $params["serverusername"];
    $service = "whostmgrd";
    return wpsquared_singlesignon($params, $user, $service);
}
function wpsquared_ClientAreaAllowedFunctions()
{
    return ["CreateEmailAccount"];
}
function wpsquared_CreateEmailAccount($params)
{
    $vars = ["cpanel_jsonapi_user" => $params["username"], "cpanel_jsonapi_apiversion" => "2", "cpanel_jsonapi_module" => "Email", "cpanel_jsonapi_func" => "addpop", "domain" => $params["domain"], "email" => App::get_req_var("email_prefix"), "password" => App::get_req_var("email_pw"), "quota" => (int) App::get_req_var("email_quota")];
    try {
        $response = wpsquared_jsonrequest($params, "/json-api/cpanel", $vars);
        $resultCode = isset($response["cpanelresult"]["event"]["result"]) ? $response["cpanelresult"]["event"]["result"] : 0;
        if($resultCode == "1") {
            return ["jsonResponse" => ["success" => true]];
        }
    } catch (WHMCS\Exception\Module\GeneralError $e) {
        return ["jsonResponse" => ["success" => false, "errorMsg" => $e->getMessage()]];
    } catch (WHMCS\Exception\Module\InvalidConfiguration $e) {
        logActivity("WP Squared Client Quick Email Create Failed: API Configuration Problem - " . $e->getMessage());
    } catch (WHMCS\Exception\Module\NotServicable $e) {
        logActivity("WP Squared Client Quick Email Create Failed: API Unreachable - " . $e->getMessage());
    } catch (WHMCS\Exception $e) {
        logActivity("WP Squared Client Quick Email Create Failed: Unknown Error - " . $e->getMessage());
    }
    return ["jsonResponse" => ["success" => false, "errorMsg" => "An error occurred. Please contact support."]];
}
function wpsquared__addErrorToList($errorMsg, array &$errors)
{
    if(!$errorMsg) {
        return NULL;
    }
    if(preg_match("/\\s+\\(XID ([a-z\\d]+)\\)\\s+/i", $errorMsg, $matches)) {
        $xidFull = trim($matches[0]);
        $xidCode = $matches[1];
        $cleanMsg = str_replace($xidFull, " ", $errorMsg);
        $errors[$cleanMsg][] = $xidCode;
    } else {
        $errors[$errorMsg] = [];
    }
}
function wpsquared__formatErrorList(array $errors)
{
    $ret = [];
    $maxXids = 5;
    foreach ($errors as $errorMsg => $xids) {
        $xidCount = is_array($xids) ? count($xids) : 0;
        if($xidCount) {
            if($maxXids < $xidCount) {
                $andMore = " and " . ($xidCount - $maxXids) . " more.";
                $xids = array_slice($xids, 0, $maxXids);
            } else {
                $andMore = "";
            }
            $xidList = " XIDs: " . implode(", ", $xids) . $andMore;
        } else {
            $xidList = "";
        }
        $ret[] = $errorMsg . $xidList;
    }
    return $ret;
}
function wpsquared_GetSupportedApplicationLinks()
{
    $appLinksData = file_get_contents(ROOTDIR . "/modules/servers/wpsquared/data/application_links.json");
    $appLinks = json_decode($appLinksData, true);
    if(array_key_exists("supportedApplicationLinks", $appLinks)) {
        return $appLinks["supportedApplicationLinks"];
    }
    return [];
}
function wpsquared_GetRemovedApplicationLinks()
{
    $appLinksData = file_get_contents(ROOTDIR . "/modules/servers/wpsquared/data/application_links.json");
    $appLinks = json_decode($appLinksData, true);
    if(array_key_exists("disabledApplicationLinks", $appLinks)) {
        return $appLinks["disabledApplicationLinks"];
    }
    return [];
}
function wpsquared_IsApplicationLinkingSupportedByServer($params)
{
    try {
        $apiResponse = wpsquared_jsonrequest($params, "/json-api/applist", "api.version=1");
        $resultCode = isset($apiResponse["metadata"]["result"]) ? $apiResponse["metadata"]["result"] : 0;
        if(!$resultCode) {
            $resultCode = isset($apiResponse["cpanelresult"]["data"]["result"]) ? $apiResponse["cpanelresult"]["data"]["result"] : 0;
        }
        if(0 < $resultCode) {
            return ["isSupported" => in_array("create_integration_link", $apiResponse["data"]["app"])];
        }
        if(isset($apiResponse["cpanelresult"]["error"])) {
            $errorMsg = $apiResponse["cpanelresult"]["error"];
        } elseif(isset($apiResponse["metadata"]["reason"])) {
            $errorMsg = $apiResponse["metadata"]["reason"];
        } else {
            $errorMsg = "Server response: " . preg_replace("/([\\d\"]),\"/", "\$1, \"", json_encode($apiResponse));
        }
    } catch (WHMCS\Exception $e) {
        $errorMsg = $e->getMessage();
    }
    return ["errorMsg" => $errorMsg];
}
function wpsquared_CreateApplicationLink($params)
{
    $systemUrl = $params["systemUrl"];
    $tokenEndpoint = $params["tokenEndpoint"];
    $clientCollection = $params["clientCredentialCollection"];
    $appLinks = $params["appLinks"];
    $stringsToMask = [];
    $commands = [];
    foreach ($clientCollection as $client) {
        $secret = $client->decryptedSecret;
        $identifier = $client->identifier;
        $apiData = ["api.version" => 1, "user" => $client->service->username, "group_id" => "whmcs", "label" => "Billing & Support", "order" => "1"];
        $commands[] = "command=create_integration_group?" . urlencode(http_build_query($apiData));
        foreach ($appLinks as $scopeName => $appLinkParams) {
            $queryParams = ["scope" => "clientarea:sso " . $scopeName, "module_type" => "server", "module" => "wpsquared"];
            $fallbackUrl = $appLinkParams["fallback_url"];
            $fallbackUrl .= (strpos($fallbackUrl, "?") ? "&" : "?") . "ssoredirect=1";
            unset($appLinkParams["fallback_url"]);
            $apiData = ["api.version" => 1, "user" => $client->service->username, "subscriber_unique_id" => $identifier, "url" => $systemUrl . $fallbackUrl, "token" => $secret, "autologin_token_url" => $tokenEndpoint . "?" . http_build_query($queryParams)];
            $commands[] = "command=create_integration_link?" . urlencode(http_build_query($apiData + $appLinkParams));
            $stringsToMask[] = urlencode(urlencode($secret));
        }
    }
    $errors = [];
    try {
        $apiResponse = wpsquared_jsonrequest($params, "/json-api/batch", "api.version=1&" . implode("&", $commands), $stringsToMask);
        if($apiResponse["metadata"]["result"] == 0) {
            foreach ($apiResponse["data"]["result"] as $key => $values) {
                if($values["metadata"]["result"] == 0) {
                    $reasonMsg = isset($values["metadata"]["reason"]) ? $values["metadata"]["reason"] : "";
                    wpsquared__adderrortolist($reasonMsg, $errors);
                }
            }
        }
    } catch (Throwable $e) {
        wpsquared__adderrortolist($e->getMessage(), $errors);
    }
    return wpsquared__formaterrorlist($errors);
}
function wpsquared_DeleteApplicationLink($params)
{
    $clientCollection = $params["clientCredentialCollection"];
    $appLinks = $params["appLinks"];
    $commands = [];
    foreach ($clientCollection as $client) {
        $apiData = ["api.version" => 1, "user" => $client->service->username, "group_id" => "whmcs"];
        $commands[] = "command=remove_integration_group?" . urlencode(http_build_query($apiData));
        foreach ($appLinks as $scopeName => $appLinkParams) {
            $apiData = ["api.version" => 1, "user" => $client->service->username, "app" => $appLinkParams["app"]];
            $commands[] = "command=remove_integration_link?" . urlencode(http_build_query($apiData));
        }
    }
    $errors = [];
    try {
        $apiResponse = wpsquared_jsonrequest($params, "/json-api/batch", "api.version=1&" . implode("&", $commands));
        if($apiResponse["metadata"]["result"] == 0) {
            foreach ($apiResponse["data"]["result"] as $key => $values) {
                if($values["metadata"]["result"] == 0) {
                    $reasonMsg = isset($values["metadata"]["reason"]) ? $values["metadata"]["reason"] : "";
                    wpsquared__adderrortolist($reasonMsg, $errors);
                }
            }
        }
    } catch (Throwable $e) {
        wpsquared__adderrortolist($e->getMessage(), $errors);
    }
    return wpsquared__formaterrorlist($errors);
}
function wpsquared_ConfirmPackageName($package, $username, array $packages)
{
    switch ($username) {
        case "":
        case "root":
            if(array_key_exists($package, $packages)) {
                return $package;
            }
            break;
        default:
            if(array_key_exists($username . "_" . $package, $packages)) {
                return $username . "_" . $package;
            }
            if(array_key_exists($package, $packages)) {
                return $package;
            }
            throw new WHMCS\Exception\Module\NotServicable("The package \"" . $package . "\" does not exist on the server");
    }
}
function wpsquared_ListPackages(array $params, $removeUsername = true)
{
    $result = wpsquared_jsonrequest($params, "/json-api/listpkgs", "");
    if(array_key_exists("cpanelresult", $result) && array_key_exists("error", $result["cpanelresult"])) {
        throw new WHMCS\Exception\Module\NotServicable($result["cpanelresult"]["error"]);
    }
    $return = [];
    if(isset($result["package"])) {
        foreach ($result["package"] as $package) {
            $packageName = $params["serverusername"] == "root" || !$removeUsername ? $package["name"] : str_replace($params["serverusername"] . "_", "", $package["name"]);
            $return[$packageName] = ucwords($packageName);
        }
    }
    return $return;
}
function wpsquared_AutoPopulateServerConfig($params)
{
    $apiResponse = wpsquared_jsonrequest($params, "/json-api/gethostname", "api.version=1");
    $hostname = $apiResponse["data"]["hostname"];
    $name = explode(".", $hostname, 2);
    $name = $name[0];
    $primaryIp = "";
    $apiResponse = wpsquared_jsonrequest($params, "/json-api/get_shared_ip", "api.version=1");
    if(array_key_exists("ip", $apiResponse["data"]) && $apiResponse["data"]["ip"]) {
        $primaryIp = trim($apiResponse["data"]["ip"]);
    }
    $assignedIps = [];
    $apiResponse = wpsquared_jsonrequest($params, "/json-api/listips", "api.version=1");
    if(isset($apiResponse["data"]["ip"]) && is_array($apiResponse["data"]["ip"])) {
        foreach ($apiResponse["data"]["ip"] as $key => $data) {
            if(trim($data["public_ip"])) {
                if(!$primaryIp && $data["mainaddr"]) {
                    $primaryIp = $data["public_ip"];
                } elseif($primaryIp != $data["public_ip"]) {
                    $assignedIps[] = $data["public_ip"];
                }
            }
        }
    }
    $apiResponse = wpsquared_jsonrequest($params, "/json-api/get_nameserver_config", "api.version=1");
    $nameservers = is_array($apiResponse["data"]["nameservers"]) ? $apiResponse["data"]["nameservers"] : [];
    return ["name" => $name, "hostname" => $hostname, "primaryIp" => $primaryIp, "assignedIps" => $assignedIps, "nameservers" => $nameservers];
}
function wpsquared_GenerateCertificateSigningRequest($params)
{
    $certificate = $params["certificateInfo"];
    if(empty($certificate["city"]) || empty($certificate["state"]) || empty($certificate["country"])) {
        throw new WHMCS\Exception("You must provide a valid city, state, and country to generate a Certificate Signing Request. Set these values in the client's profile and try again.");
    }
    $command = "/json-api/cpanel";
    $postVars = ["keysize" => "2048", "friendly_name" => $certificate["domain"] . time(), "cpanel_jsonapi_user" => strtolower($params["username"]), "cpanel_jsonapi_apiversion" => "3", "cpanel_jsonapi_module" => "SSL", "cpanel_jsonapi_func" => "generate_key"];
    $response = wpsquared_jsonrequest($params, $command, $postVars);
    if($response["result"]["errors"]) {
        $error = is_array($response["result"]["errors"]) ? implode(". ", $response["result"]["errors"]) : $response["result"]["errors"];
        throw new WHMCS\Exception("WP Squared: Key Generation Failed: " . $error);
    }
    $keyId = $response["result"]["data"]["id"];
    $postVars = ["domains" => $certificate["domain"], "countryName" => $certificate["country"], "stateOrProvinceName" => $certificate["state"], "localityName" => $certificate["city"], "organizationName" => $certificate["orgname"] ?: "N/A", "organizationalUnitName" => $certificate["orgunit"], "emailAddress" => $certificate["email"], "key_id" => $keyId, "cpanel_jsonapi_user" => strtolower($params["username"]), "cpanel_jsonapi_apiversion" => "3", "cpanel_jsonapi_module" => "SSL", "cpanel_jsonapi_func" => "generate_csr"];
    $response = wpsquared_jsonrequest($params, $command, $postVars);
    if(isset($response["result"]["status"]) && $response["result"]["status"] == 1) {
        $csr = $response["result"]["data"]["text"];
        return $csr;
    }
    $errorMsg = isset($response["result"]["errors"]) ? is_array($response["result"]["errors"]) ? implode(". ", $response["result"]["errors"]) : $response["result"]["errors"] : json_encode($response);
    throw new WHMCS\Exception("WP Squared: CSR Generation Failed: " . $errorMsg);
}
function wpsquared_GetDocRoot($params)
{
    $command = "/json-api/cpanel";
    $postVars = ["cpanel_jsonapi_user" => strtolower($params["username"]), "cpanel_jsonapi_apiversion" => "2", "cpanel_jsonapi_module" => "DomainLookup", "cpanel_jsonapi_func" => "getdocroot", "domain" => $params["domain"]];
    $response = wpsquared_jsonrequest($params, $command, $postVars);
    if(isset($response["cpanelresult"]["error"]) && $response["cpanelresult"]["error"]) {
        throw new WHMCS\Exception("WP Squared: Unable to locate docroot: " . json_encode($response));
    }
    return $response["cpanelresult"]["data"][0]["docroot"];
}
function wpsquared_CreateFileWithinDocRoot($params)
{
    $command = "/json-api/cpanel";
    $postVars = ["cpanel_jsonapi_user" => strtolower($params["username"]), "cpanel_jsonapi_apiversion" => "2", "cpanel_jsonapi_module" => "DomainLookup", "cpanel_jsonapi_func" => "getdocroot", "domain" => $params["certificateDomain"]];
    $response = wpsquared_jsonrequest($params, $command, $postVars);
    if(isset($response["cpanelresult"]["error"]) && $response["cpanelresult"]["error"]) {
        throw new WHMCS\Exception("WP Squared: Unable to locate docroot: " . json_encode($response));
    }
    $dir = array_key_exists("dir", $params) ? $params["dir"] : "";
    $basePath = $response["cpanelresult"]["data"][0]["reldocroot"];
    if($dir) {
        $dirParts = explode("/", $dir);
        foreach ($dirParts as $dirPart) {
            $command = "/json-api/cpanel";
            $postVars = ["cpanel_jsonapi_user" => strtolower($params["username"]), "cpanel_jsonapi_apiversion" => "2", "cpanel_jsonapi_module" => "Fileman", "cpanel_jsonapi_func" => "mkdir", "path" => $basePath, "name" => $dirPart];
            try {
                wpsquared_jsonrequest($params, $command, $postVars);
            } catch (Exception $e) {
                if(stripos($e->getMessage(), "file exists") === false) {
                    throw $e;
                }
            }
            $basePath .= "/" . $dirPart;
        }
    }
    $command = "/json-api/cpanel";
    $postVars = ["cpanel_jsonapi_user" => strtolower($params["username"]), "cpanel_jsonapi_apiversion" => "3", "cpanel_jsonapi_module" => "Fileman", "cpanel_jsonapi_func" => "save_file_content", "dir" => $basePath, "file" => $params["filename"], "from_charset" => "utf-8", "to_charset" => "utf-8", "content" => $params["fileContent"]];
    $response = wpsquared_jsonrequest($params, $command, $postVars);
    if(isset($response["result"]["errors"]) && $response["result"]["errors"]) {
        throw new WHMCS\Exception("WP Squared: Unable to create DV Auth File: " . json_encode($response));
    }
}
function wpsquared_InstallSsl($params)
{
    $command = "/json-api/cpanel";
    $postVars = ["certificate" => $params["certificate"], "cpanel_jsonapi_user" => strtolower($params["username"]), "cpanel_jsonapi_apiversion" => "3", "cpanel_jsonapi_module" => "SSL", "cpanel_jsonapi_func" => "fetch_key_and_cabundle_for_certificate"];
    $response = wpsquared_jsonrequest($params, $command, $postVars);
    if($response["result"]["status"] == 0) {
        throw new WHMCS\Exception($response["result"]["messages"]);
    }
    $key = $response["data"]["key"];
    $postVars = ["domain" => $params["certificateDomain"], "cert" => $params["certificate"], "key" => $key, "cpanel_jsonapi_user" => strtolower($params["username"]), "cpanel_jsonapi_apiversion" => "3", "cpanel_jsonapi_module" => "SSL", "cpanel_jsonapi_func" => "install_ssl"];
    $response = wpsquared_jsonrequest($params, $command, $postVars);
    if($response["result"]["status"] == 0) {
        if($response["result"]["messages"]) {
            if(is_array($response["result"]["messages"])) {
                $error = implode(" ", $response["result"]["messages"]);
            } else {
                $error = $response["result"]["messages"];
            }
        } elseif($response["result"]["errors"]) {
            if(is_array($response["result"]["errors"])) {
                $error = implode(" ", $response["result"]["errors"]);
            } else {
                $error = $response["result"]["errors"];
            }
        } else {
            $error = "An unknown error occurred";
        }
        throw new WHMCS\Exception($error);
    }
}
function wpsquared_GetDns(array $params)
{
    $command = "/json-api/cpanel";
    $postVars = ["cpanel_jsonapi_user" => strtolower($params["username"]), "cpanel_jsonapi_apiversion" => "2", "cpanel_jsonapi_module" => "ZoneEdit", "cpanel_jsonapi_func" => "fetchzone_records", "domain" => $params["domain"]];
    $response = wpsquared_jsonrequest($params, $command, $postVars);
    if(array_key_exists("error", $response["cpanelresult"]) && $response["cpanelresult"]["error"]) {
        $error = is_array($response["cpanelresult"]["error"]) ? implode(". ", $response["cpanelresult"]["error"]) : $response["cpanelresult"]["error"];
        throw new WHMCS\Exception("Unable to Get DNS: " . $error);
    }
    if(isset($response["cpanelresult"]["data"]) && is_array($response["cpanelresult"]["data"])) {
        return $response["cpanelresult"]["data"];
    }
    throw new WHMCS\Exception("Unexpected response for Get DNS: " . json_encode($response));
}
function wpsquared_SetDnsRecord(array $params)
{
    $command = "/json-api/cpanel";
    $postVars = ["cpanel_jsonapi_user" => strtolower($params["username"]), "cpanel_jsonapi_apiversion" => "2", "cpanel_jsonapi_module" => "ZoneEdit", "cpanel_jsonapi_func" => "edit_zone_record", "domain" => $params["domain"]];
    $dnsRecord = is_array($params["dnsRecord"]) ? $params["dnsRecord"] : [];
    $postVars = array_merge($postVars, $dnsRecord);
    $response = wpsquared_jsonrequest($params, $command, $postVars);
    if(array_key_exists("error", $response["cpanelresult"]) && $response["cpanelresult"]["error"]) {
        $error = is_array($response["cpanelresult"]["error"]) ? implode(". ", $response["cpanelresult"]["error"]) : $response["cpanelresult"]["error"];
        throw new WHMCS\Exception("Unable to Modify DNS: " . $error);
    }
    if(isset($response["cpanelresult"]["data"][0]["result"]["status"]) && $response["cpanelresult"]["data"][0]["result"]["status"] == 0) {
        throw new WHMCS\Exception("Unable to Modify DNS: " . $response["cpanelresult"]["data"][0]["result"]["statusmsg"]);
    }
}
function wpsquared_ModifyDns(array $params)
{
    $serverDnsRecords = wpsquared_getdns($params);
    $recordsToCreate = [];
    $dnsRecordsToProvision = $params["dnsRecordsToProvision"];
    foreach ($dnsRecordsToProvision as $recordToProvision) {
        if(!$recordToProvision["name"] && !$recordToProvision["host"]) {
            if(0 < count($recordsToCreate)) {
                unset($params["dnsRecordsToProvision"]);
                $params["dnsRecords"] = $recordsToCreate;
                wpsquared_AddDns($params);
            }
        } else {
            $recordToUpdate = NULL;
            $dnsHost = $recordToProvision["name"] ?: $recordToProvision["host"];
            foreach ($serverDnsRecords as $existingRecord) {
                if($existingRecord["type"] == $recordToProvision["type"] && wpsquared__normaliseHostname($existingRecord, $params["domain"]) == $dnsHost) {
                    $recordToUpdate = $existingRecord;
                    if(is_null($recordToUpdate)) {
                        $recordsToCreate[] = ["name" => $dnsHost, "type" => $recordToProvision["type"], "value" => $recordToProvision["value"]];
                    } else {
                        if(in_array($recordToProvision["type"], ["A"])) {
                            $recordToUpdate["address"] = $recordToProvision["value"];
                        } elseif(in_array($recordToProvision["type"], ["CNAME"])) {
                            $recordToUpdate["cname"] = $recordToProvision["value"];
                        } elseif(in_array($recordToProvision["type"], ["TXT", "SRV"])) {
                            $recordToUpdate["txtdata"] = $recordToProvision["value"];
                        }
                        $params["dnsRecord"] = $recordToUpdate;
                        wpsquared_setdnsrecord($params);
                        unset($params["dnsRecord"]);
                    }
                }
            }
        }
    }
}
function wpsquared_create_api_token(array $params)
{
    $tokenName = "WHMCS" . App::getLicense()->getLicenseKey() . genRandomVal(5);
    $command = "/json-api/api_token_create";
    $postVars = ["api.version" => 1, "token_name" => $tokenName];
    try {
        $response = wpsquared_jsonrequest($params, $command, $postVars);
    } catch (Throwable $e) {
        return ["success" => false, $e->getMessage()];
    }
    if($response["metadata"]["result"] == 1) {
        return ["success" => true, "api_token" => $response["data"]["token"]];
    }
    return ["success" => false, "error" => $response["metadata"]["reason"]];
}
function wpsquared_request_backup(array $params)
{
    $command = "/json-api/cpanel";
    switch ($params["dest"]) {
        case "passiveftp":
            $postVarsData = ["variant" => "passive", "username" => $params["user"], "password" => $params["pass"], "host" => $params["hostname"], "port" => $params["port"], "directory" => $params["rdir"], "email" => $params["email"]];
            $dest = "_to_ftp";
            break;
        case "scp":
            $postVarsData = ["username" => $params["user"], "password" => $params["pass"], "host" => $params["hostname"], "port" => $params["port"], "directory" => $params["rdir"], "email" => $params["email"]];
            $dest = "_to_scp_with_password";
            break;
        case "homedir":
            $postVarsData = ["email" => $params["email"]];
            $dest = "_to_homedir";
            break;
        default:
            $postVarsData = ["username" => $params["user"], "password" => $params["pass"], "host" => $params["hostname"], "port" => $params["port"], "directory" => $params["rdir"], "email" => $params["email"]];
            $dest = "_to_ftp";
            $postVarsConnData = ["cpanel_jsonapi_user" => strtolower($params["username"]), "cpanel_jsonapi_apiversion" => "3", "cpanel_jsonapi_module" => "Backup", "cpanel_jsonapi_func" => "fullbackup" . $dest];
            $postVars = array_merge($postVarsData, $postVarsConnData);
            $response = wpsquared_jsonrequest($params, $command, $postVars);
            if(array_key_exists("errors", $response["result"]) && $response["result"]["errors"]) {
                $error = is_array($response["result"]["errors"]) ? implode(". ", $response["result"]["errors"]) : $response["result"]["errors"];
                throw new WHMCS\Exception("Unable to Request Backup: " . $error);
            }
    }
}
function wpsquared_list_ssh_keys(array $params)
{
    $command = "/json-api/cpanel";
    $postVars = ["pub" => 0, "cpanel_jsonapi_user" => strtolower($params["username"]), "cpanel_jsonapi_apiversion" => "2", "cpanel_jsonapi_module" => "SSH", "cpanel_jsonapi_func" => "listkeys"];
    if(array_key_exists("key_name", $params)) {
        $postVars["keys"] = $params["key_name"];
    }
    if(array_key_exists("key_encryption_type", $params) && in_array($params["key_encryption_type"], ["rsa", "dsa"])) {
        $postVars["types"] = $params["key_encryption_type"];
    }
    if(array_key_exists("public_key", $params) && $params["public_key"]) {
        $postVars["pub"] = 1;
    }
    $response = wpsquared_jsonrequest($params, $command, $postVars);
    $response = $response["cpanelresult"];
    if(!$response["event"]["result"]) {
        throw new WHMCS\Exception("Unable to Request SSH Key List: " . $response["event"]["reason"]);
    }
    return $response;
}
function wpsquared_generate_ssh_key(array $params)
{
    $command = "/json-api/cpanel";
    $bits = 2048;
    if(array_key_exists("bits", $params)) {
        $bits = $params["bits"];
    }
    $postVars = ["cpanel_jsonapi_user" => strtolower($params["username"]), "cpanel_jsonapi_apiversion" => "2", "cpanel_jsonapi_module" => "SSH", "cpanel_jsonapi_func" => "genkey", "name" => $params["key_name"], "bits" => $bits];
    $response = wpsquared_jsonrequest($params, $command, $postVars);
    $response = $response["cpanelresult"];
    if(!$response["event"]["result"]) {
        throw new WHMCS\Exception("Unable to Generate SSH Key: " . $response["event"]["reason"]);
    }
}
function wpsquared_fetch_ssh_key(array $params)
{
    $command = "/json-api/cpanel";
    $postVars = ["cpanel_jsonapi_user" => strtolower($params["username"]), "cpanel_jsonapi_apiversion" => "2", "cpanel_jsonapi_module" => "SSH", "cpanel_jsonapi_func" => "fetchkey", "name" => $params["key_name"], "pub" => 0];
    if(array_key_exists("public_key", $params) && $params["public_key"]) {
        $postVars["pub"] = 1;
    }
    $response = wpsquared_jsonrequest($params, $command, $postVars);
    $response = $response["cpanelresult"];
    if(!$response["event"]["result"]) {
        throw new WHMCS\Exception("Unable to Fetch SSH Key: " . $response["event"]["reason"]);
    }
    $keyData = $response["data"][0];
    $postVars = ["cpanel_jsonapi_user" => strtolower($params["username"]), "cpanel_jsonapi_apiversion" => "2", "cpanel_jsonapi_module" => "SSH", "cpanel_jsonapi_func" => "authkey", "key" => $keyData["name"], "action" => "authorize"];
    wpsquared_jsonrequest($params, $command, $postVars);
    return $keyData;
}
function wpsquared_get_ssh_port(array $params)
{
    $command = "/json-api/cpanel";
    $postVars = ["cpanel_jsonapi_user" => strtolower($params["username"]), "cpanel_jsonapi_apiversion" => "3", "cpanel_jsonapi_module" => "SSH", "cpanel_jsonapi_func" => "get_port"];
    $response = wpsquared_jsonrequest($params, $command, $postVars);
    $response = $response["result"];
    if(!$response["status"]) {
        throw new WHMCS\Exception("Unable to Fetch SSH Port Number: " . $response["messages"]);
    }
    return $response["data"]["port"];
}
function wpsquared_ListAccounts(array $params)
{
    $command = "/json-api/listaccts";
    $postVars = ["want" => "domain,user,plan,ip,unix_startdate,suspended,email,owner"];
    $accounts = [];
    try {
        $hasAllPerm = wpsquared_hasEverythingPerm($params);
        $availablePackages = wpsquared_listpackages($params);
        $response = wpsquared_jsonrequest($params, $command, $postVars);
        if($response["status"] == 1) {
            foreach ($response["acct"] as $userAccount) {
                if($userAccount["owner"] != $params["serverusername"] && $userAccount["owner"] != $userAccount["user"]) {
                } else {
                    $status = WHMCS\Utility\Status::ACTIVE;
                    if($userAccount["suspended"]) {
                        $status = WHMCS\Utility\Status::SUSPENDED;
                    }
                    $plan = $userAccount["plan"];
                    if($params["serverusername"] != "root" && !stristr($plan, $params["serverusername"]) && !$hasAllPerm && in_array($plan, $availablePackages)) {
                        $plan = $params["serverusername"] . "_" . $plan;
                    }
                    $createdDate = NULL;
                    try {
                        $startDate = $userAccount["unix_startdate"];
                        if(is_numeric($startDate) && (int) $startDate !== 0) {
                            $startDateObject = WHMCS\Carbon::createFromTimestamp($startDate);
                            if($startDateObject) {
                                $createdDate = $startDateObject->toDateTimeString();
                            }
                        }
                    } catch (Exception $e) {
                    }
                    if(!$createdDate) {
                        $createdDate = WHMCS\Carbon::today()->toDateTimeString();
                    }
                    $account = ["name" => $userAccount["user"], "email" => $userAccount["email"], "username" => $userAccount["user"], "domain" => $userAccount["domain"], "uniqueIdentifier" => $userAccount["domain"], "product" => $plan, "primaryip" => $userAccount["ip"], "created" => $createdDate, "status" => $status];
                    $accounts[] = $account;
                }
            }
            return ["success" => true, "accounts" => $accounts];
        } else {
            return ["success" => false, "accounts" => $accounts, "error" => $response["metadata"]["reason"]];
        }
    } catch (Exception $e) {
        return ["success" => false, "accounts" => $accounts, "error" => $e->getMessage()];
    }
}
function wpsquared_getUserData(array $params)
{
    $command = "/json-api/listaccts";
    $postVars = ["searchtype" => "user", "search" => $params["username"], "want" => "domain,user,plan,ip,suspended,email,owner"];
    $accountData = [];
    try {
        $results = wpsquared_jsonrequest($params, $command, $postVars);
        if($results["status"] == 1) {
            $userData = $results["acct"][0];
            $accountData = ["name" => $userData["user"], "email" => $userData["email"], "username" => $userData["user"], "domain" => $userData["domain"], "uniqueIdentifier" => $userData["domain"], "product" => $userData["plan"]];
            return ["success" => true, "userData" => $accountData];
        }
        return ["success" => false, "userData" => $accountData, "error" => $results["metadata"]["reason"]];
    } catch (Exception $e) {
        return ["success" => false, "userData" => $accountData, "error" => $e->getMessage()];
    }
}
function wpsquared_GetUserCount(array $params)
{
    $command = "/json-api/listaccts";
    $postVars = ["want" => "user,owner"];
    try {
        $response = wpsquared_jsonrequest($params, $command, $postVars);
        if($response["status"] == 1) {
            $totalCount = count($response["acct"]);
            $ownedAccounts = 0;
            foreach ($response["acct"] as $userAccount) {
                if($userAccount["owner"] == $params["serverusername"] || $userAccount["owner"] == $userAccount["user"]) {
                    $ownedAccounts++;
                }
            }
            return ["success" => true, "totalAccounts" => $totalCount, "ownedAccounts" => $ownedAccounts];
        } else {
            throw new Exception(!empty($response["statusmsg"]) ? $response["statusmsg"] : "The server encountered an unknown error");
        }
    } catch (Exception $e) {
        return ["success" => false, "error" => $e->getMessage()];
    }
}
function wpsquared_GetRemoteMetaData(array $params)
{
    $errors = [];
    try {
        $apiData = urlencode(http_build_query(["api.version" => 1]));
        $commands[] = "command=version?" . $apiData;
        $commands[] = "command=systemloadavg?" . $apiData;
        $commands[] = "command=get_maximum_users?" . $apiData;
        $apiResponse = wpsquared_jsonrequest($params, "/json-api/batch", "api.version=1&" . implode("&", $commands));
        if($apiResponse["metadata"]["result"] == 0) {
            foreach ($apiResponse["data"]["result"] as $key => $values) {
                if($values["metadata"]["result"] == 0) {
                    $reasonMsg = "";
                    if(isset($values["metadata"]["reason"])) {
                        $reasonMsg = $values["metadata"]["reason"];
                    }
                    if(substr($reasonMsg, 0, 11) !== "Unknown app") {
                        wpsquared__adderrortolist($reasonMsg, $errors);
                    }
                }
            }
        }
        wpsquared__assertValidProfileRemote($params, $errors);
        $errors = wpsquared__formaterrorlist($errors);
        if(0 < count($errors)) {
            return ["success" => false, "error" => implode(", ", $errors)];
        }
        $version = "-";
        $loads = ["fifteen" => "0", "five" => "0", "one" => "0"];
        $maxUsers = "0";
        foreach ($apiResponse["data"]["result"] as $key => $values) {
            if(!array_key_exists("data", $values)) {
            } else {
                switch ($values["metadata"]["command"]) {
                    case "get_maximum_users":
                        $maxUsers = $values["data"]["maximum_users"];
                        break;
                    case "systemloadavg":
                        $loads = $values["data"];
                        break;
                    case "version":
                        $version = $values["data"]["version"];
                        break;
                }
            }
        }
        return ["version" => $version, "load" => $loads, "max_accounts" => $maxUsers];
    } catch (Exception $e) {
        return ["success" => false, "error" => $e->getMessage()];
    }
}
function wpsquared_RenderRemoteMetaData(array $params)
{
    $remoteData = $params["remoteData"];
    if($remoteData) {
        $metaData = $remoteData->metaData;
        $version = "Unknown";
        $loadOne = $loadFive = $loadFifteen = 0;
        $maxAccounts = "Unlimited";
        if(array_key_exists("version", $metaData)) {
            $version = WHMCS\Input\Sanitize::encode($metaData["version"]);
        }
        if(array_key_exists("load", $metaData)) {
            $loadOne = WHMCS\Input\Sanitize::encode($metaData["load"]["one"]);
            $loadFive = WHMCS\Input\Sanitize::encode($metaData["load"]["five"]);
            $loadFifteen = WHMCS\Input\Sanitize::encode($metaData["load"]["fifteen"]);
        }
        if(array_key_exists("max_accounts", $metaData) && 0 < $metaData["max_accounts"]) {
            $maxAccounts = WHMCS\Input\Sanitize::encode($metaData["max_accounts"]);
        }
        return "WP Squared Version: " . $version . "<br>\nLoad Averages: " . $loadOne . " " . $loadFive . " " . $loadFifteen . "<br>\nLicense Max # of Accounts: " . $maxAccounts;
    }
    return "";
}
function wpsquared_MetricItems()
{
    static $items = NULL;
    $transName = function ($key) {
        if(App::isAdminAreaRequest()) {
            return AdminLang::trans($key);
        }
        return Lang::trans($key);
    };
    if(!$items) {
        $items = [new WHMCS\UsageBilling\Metrics\Metric("diskusage", $transName("usagebilling.metric.diskSpace"), WHMCS\UsageBilling\Contracts\Metrics\MetricInterface::TYPE_SNAPSHOT, new WHMCS\UsageBilling\Metrics\Units\GigaBytes()), new WHMCS\UsageBilling\Metrics\Metric("bandwidthusage", $transName("usagebilling.metric.bandwidth"), WHMCS\UsageBilling\Contracts\Metrics\MetricInterface::TYPE_PERIOD_MONTH, new WHMCS\UsageBilling\Metrics\Units\GigaBytes()), new WHMCS\UsageBilling\Metrics\Metric("addondomains", $transName("usagebilling.metric.wpInstances"), WHMCS\UsageBilling\Contracts\Metrics\MetricInterface::TYPE_SNAPSHOT, new WHMCS\UsageBilling\Metrics\Units\Domains("Wordpress Instances", "Instance", "Instances")), new WHMCS\UsageBilling\Metrics\Metric("mysqldiskusage", $transName("usagebilling.metric.mysqlDiskUsage"), WHMCS\UsageBilling\Contracts\Metrics\MetricInterface::TYPE_SNAPSHOT, new WHMCS\UsageBilling\Metrics\Units\GigaBytes())];
    }
    return $items;
}
function wpsquared_MetricProvider(array $params)
{
    $items = wpsquared_metricitems();
    $serverUsage = function (WHMCS\UsageBilling\Contracts\Metrics\ProviderInterface $provider, $tenant = NULL) use($params) {
        $usage = [];
        try {
            $accounts = wpsquared_listaccounts($params);
        } catch (Throwable $e) {
            return $e->getMessage();
        }
        if(empty($accounts["accounts"])) {
            return $usage;
        }
        $tenants = [];
        $usernames = [];
        foreach ($accounts["accounts"] as $account) {
            if(!empty($account["username"])) {
                $tenants[$account["username"]] = $account["domain"];
            }
        }
        $metrics = $provider->metrics();
        foreach ($tenants as $username => $domain) {
            if($tenant && $tenant != $domain) {
            } else {
                $usernames[] = $username;
            }
        }
        $useGetStats = false;
        try {
            $params["usernames"] = $usernames;
            $results = wpsquared_GetStatsUAPI($params);
        } catch (WHMCS\Exception $e) {
            $useGetStats = true;
        }
        if($useGetStats) {
            $results = [];
            foreach ($usernames as $username) {
                $params["username"] = $username;
                $results[$username] = wpsquared_GetStats($params);
            }
        }
        if($tenant && count($results) === 0) {
            throw new WHMCS\Exception\Module\NotServicable("Unable to refresh metrics. Make certain that you are the account owner.");
        }
        foreach ($results as $username => $data) {
            $domain = $tenants[$username];
            foreach ($data as $stat) {
                $name = $stat["id"];
                if(isset($metrics[$name])) {
                    $metric = $metrics[$name];
                    $remoteValue = $stat["_count"];
                    if(isset($stat["units"]) && in_array($stat["units"], ["MB", "GB", "KB", "B"])) {
                        $units = $metric->units();
                        $to = $units->suffix();
                        if($name == "mysqldiskusage") {
                            $from = "B";
                        } else {
                            $from = $stat["units"];
                        }
                        $remoteValue = $units::convert($remoteValue, $from, $to);
                    }
                    $usage[$domain][$name] = $metric->withUsage(new WHMCS\UsageBilling\Metrics\Usage($remoteValue));
                }
            }
        }
        return $usage;
    };
    $tenantUsage = function ($tenant, WHMCS\UsageBilling\Contracts\Metrics\ProviderInterface $provider) use($params, $serverUsage) {
        $usage = call_user_func($serverUsage, $provider, $tenant);
        if(isset($usage[$tenant])) {
            return $usage[$tenant];
        }
        return [];
    };
    $provider = new WHMCS\UsageBilling\Metrics\Providers\CallbackUsage($items, $serverUsage, $tenantUsage);
    return $provider;
}
function wpsquared_GetStatsUAPI(array $params)
{
    $usernames = $params["usernames"];
    $apiData = ["api.version" => "1", "cpanel.module" => "StatsBar", "cpanel.function" => "get_stats", "cpanel.user" => strtolower($usernames[0] ?? "")];
    $response = wpsquared_jsonrequest($params, "json-api/uapi_cpanel", $apiData);
    if($response["metadata"]["result"] == 0) {
        throw new WHMCS\Exception($response["metadata"]["reason"]);
    }
    $commands = [];
    foreach ($usernames as $username) {
        $apiData = ["cpanel.module" => "StatsBar", "cpanel.function" => "get_stats", "cpanel.user" => strtolower($username), "display" => "addondomains|bandwidthusage|diskusage|mysqldiskusage|postgresdiskusage"];
        $commands[] = "command=uapi_cpanel?" . urlencode(http_build_query($apiData));
    }
    $response = wpsquared_jsonrequest($params, "/json-api/batch", "api.version=1&" . implode("&", $commands));
    $data = [];
    foreach ($usernames as $key => $username) {
        $data[$username] = $response["data"]["result"][$key]["data"]["uapi"]["data"];
    }
    return $data;
}
function wpsquared_GetStats(array $params)
{
    $command = "/json-api/cpanel";
    $postVars = ["display" => "addondomains|bandwidthusage|diskusage|mysqldiskusage|postgresdiskusage", "cpanel_jsonapi_user" => strtolower($params["username"]), "cpanel_jsonapi_apiversion" => "3", "cpanel_jsonapi_module" => "StatsBar", "cpanel_jsonapi_func" => "get_stats"];
    $response = wpsquared_jsonrequest($params, $command, $postVars);
    if(!empty($response["result"]["errors"])) {
        $error = is_array($response["result"]["errors"]) ? implode(". ", $response["result"]["errors"]) : $response["result"]["errors"];
        throw new WHMCS\Exception("Unable to get stats: " . $error);
    }
    $data = !empty($response["result"]["data"]) && is_array($response["result"]["data"]) ? $response["result"]["data"] : [];
    return $data;
}
function wpsquared_hasEverythingPerm($params)
{
    $command = "/json-api/myprivs";
    $postVars = ["api.version" => "1"];
    $output = wpsquared_jsonrequest($params, $command, $postVars);
    if(is_array($output)) {
        $hasAllPerm = $output["data"]["privileges"][0]["all"];
        if($hasAllPerm === 1) {
            return true;
        }
    }
    return false;
}
function wpsquared_AddDns(array $params)
{
    $requiredData = function ($dnsRecord) {
        $requiredData = [];
        switch ($dnsRecord["type"]) {
            case "A":
            case "AAAA":
                $requiredData["address"] = $dnsRecord["value"];
                break;
            case "CNAME":
                $requiredData["cname"] = $dnsRecord["value"];
                break;
            case "TXT":
            case "SRV":
                $requiredData["txtdata"] = $dnsRecord["value"];
                break;
            case "MX":
                $requiredData["exchange"] = $dnsRecord["value"];
                $requiredData["preference"] = $dnsRecord["opt"];
                break;
            default:
                unset($dnsRecord["value"]);
                return $requiredData;
        }
    };
    $dnsRecords = [];
    foreach ($params["dnsRecords"] as $dnsRecord) {
        if(!$dnsRecord["type"] && !$dnsRecord["value"]) {
        } else {
            $dnsRecords[] = array_merge(["domain" => $params["domain"], "type" => $dnsRecord["type"], "name" => wpsquared__normaliseHostname($dnsRecord, $params["domain"])], $requiredData($dnsRecord));
        }
    }
    unset($params["dnsRecords"]);
    foreach ($dnsRecords as $dnsRecord) {
        $command = "/json-api/addzonerecord";
        $response = wpsquared_jsonrequest($params, $command, $dnsRecord);
        if(isset($response["result"][0]["status"]) && $response["result"][0]["status"] == 0) {
            throw new WHMCS\Exception("Unable to Add DNS Records: " . $response["result"][0]["statusmsg"]);
        }
    }
}
function wpsquared__normaliseHostname(array $dnsRecord, string $domain)
{
    if(!$dnsRecord["name"] && !$dnsRecord["host"]) {
        return false;
    }
    $dnsHost = $dnsRecord["name"] ?: $dnsRecord["host"];
    $dnsHost = trim($dnsHost, ".");
    $length = -1 * strlen($domain);
    if(substr($dnsHost, $length) == $domain) {
        $dnsHost = substr($dnsHost, 0, $length);
    }
    return trim($dnsHost, ".");
}
function wpsquared_CustomActions(array $params)
{
    $serviceIsActive = $params["model"]->status === WHMCS\Service\Service::STATUS_ACTIVE;
    return (new WHMCS\Module\Server\CustomActionCollection())->add(WHMCS\Module\Server\CustomAction::factory("wpsquared", "wpsquared.login", "wpsquared_SingleSignOn", [$params, $params["username"], "cpaneld"], ["productsso"], $serviceIsActive));
}
function wpsquared__determineRequestAddress($whmIpAddress = "", string $whmHostName)
{
    if(!empty($whmIpAddress)) {
        return WHMCS\Http\IpUtils::isValidIPv6($whmIpAddress) ? sprintf("[%s]", $whmIpAddress) : $whmIpAddress;
    }
    return $whmHostName;
}
function wpsquared__assertValidProfileRemote(array $params, &$errors)
{
    $profile = wpsquared__getCurrentProfileRemote($params);
    try {
        $profile->assertValidProfile(WHMCS\Module\Server\WpSquared\WpSquared\ServerProfile::SERVER_PROFILE);
    } catch (WHMCS\Exception\Module\NotServicable $e) {
        wpsquared__adderrortolist($e->getMessage(), $errors);
        logActivity(sprintf("%s - Server ID: %d", $e->getMessage(), $params["serverid"]));
    }
}
function wpsquared__getCurrentProfileRemote($params) : WHMCS\Module\Server\WpSquared\WpSquared\ServerProfile
{
    $profile = new WHMCS\Module\Server\WpSquared\WpSquared\ServerProfile("", "", "");
    try {
        $response = wpsquared_jsonrequest($params, "/json-api/get_current_profile", ["api.version" => 1]);
        if(is_array($response)) {
            $profile = new WHMCS\Module\Server\WpSquared\WpSquared\ServerProfile($response["data"]["code"], $response["data"]["name"], $response["data"]["description"]);
        }
    } catch (Throwable $e) {
    }
    return $profile;
}
function wpsquared__getCurrentProfile($params) : WHMCS\Module\Server\WpSquared\WpSquared\ServerProfile
{
    $cachedDataKey = sprintf("WPSquaredServerProfile-%s", $params["serverhostname"]);
    $profile = WHMCS\TransientData::getInstance()->retrieve($cachedDataKey);
    if(!$profile) {
        $profile = wpsquared__getcurrentprofileremote($params);
        WHMCS\TransientData::getInstance()->store($cachedDataKey, json_encode($profile->toArray()), 60 * WHMCS\Module\Server\WpSquared\WpSquared\ServerProfile::SERVER_PROFILE_CACHE_MINUTES);
    } else {
        $profileAsJson = json_decode($profile);
        $profile = WHMCS\Module\Server\WpSquared\WpSquared\ServerProfile::factory($profileAsJson->code, $profileAsJson->name, $profileAsJson->description);
    }
    return $profile;
}
function wpsquared__assertValidProfile($params)
{
    $profile = wpsquared__getcurrentprofile($params);
    return $profile->assertValidProfile(WHMCS\Module\Server\WpSquared\WpSquared\ServerProfile::SERVER_PROFILE);
}

?>