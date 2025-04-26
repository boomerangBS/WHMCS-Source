<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
function resellercentral_MetaData()
{
    return ["DisplayName" => "Reseller Central", "APIVersion" => "1.0"];
}
function resellercentral_ConfigOptions()
{
    $configarray = ["API Key" => ["Type" => "text", "Size" => "60"], "Package Name" => ["Type" => "text", "Size" => "20"], "Location" => ["Type" => "dropdown", "Options" => "US-EAST,US-CENTRAL,US-WEST,UK,ASIA,US-CLOUD"], "Platform" => ["Type" => "dropdown", "Options" => "Linux,Windows"]];
    return $configarray;
}
function resellercentral_CreateAccount($params)
{
    $location = $params["configoption3"];
    if($params["customfields"]["Website Location"]) {
        $location = $params["customfields"]["Website Location"];
    }
    if($location == "Chicago (USA)") {
        $location = 4;
    } elseif($location == "Georgia (USA)") {
        $location = 4;
    } elseif($location == "Texas (USA)") {
        $location = 4;
    } elseif($location == "Berkshire (UK)") {
        $location = 5;
    } elseif($location == "Washington DC (USA)") {
        $location = 6;
    } elseif($location == "New York (USA)") {
        $location = 6;
    } elseif($location == "California (USA)") {
        $location = 8;
    } elseif($location == "Singapore (ASIA)") {
        $location = 10;
    } elseif($location == "US-EAST") {
        $location = 6;
    } elseif($location == "US-CENTRAL") {
        $location = 4;
    } elseif($location == "US-WEST") {
        $location = 8;
    } elseif($location == "UK") {
        $location = 5;
    } elseif($location == "ASIA") {
        $location = 10;
    } elseif($location == "US-CLOUD") {
        $location = 9;
    } else {
        return "No Matching Location Found";
    }
    $fields = ["action" => "create_account", "api_key" => $params["configoption1"], "domain" => $params["domain"], "username" => $params["username"], "password" => $params["password"], "email" => $params["clientsdetails"]["email"], "location" => $location, "package" => $params["configoption2"]];
    if($params["configoption4"] == "Windows") {
        $fields["platform"] = "2";
    }
    $result = resellercentral_req($fields, $params["packageid"], $params["accountid"], $params);
    return $result;
}
function resellercentral_SuspendAccount($params)
{
    $fields = ["action" => "suspend_account", "api_key" => $params["configoption1"], "domain" => $params["domain"]];
    $result = resellercentral_req($fields);
    return $result;
}
function resellercentral_UnsuspendAccount($params)
{
    $fields = ["action" => "unsuspend_account", "api_key" => $params["configoption1"], "domain" => $params["domain"]];
    $result = resellercentral_req($fields);
    return $result;
}
function resellercentral_req($fields, $packageid = "", $accountid = "", array $params = [])
{
    $action = $fields["action"];
    if($action == "create_account") {
        $creatingaccount = true;
    }
    $url = "http://cp.hostnine.com/api/" . $action . ".php?";
    unset($fields["action"]);
    $fieldstring = "";
    foreach ($fields as $key => $value) {
        $url .= $key . "=" . urlencode($value) . "&";
    }
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 200);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    $data = curl_exec($ch);
    if(curl_errno($ch)) {
        $data = curl_errno($ch) . " - " . curl_error($ch);
    }
    curl_close($ch);
    if(!$data) {
        $data = "No Response from API";
    }
    logModuleCall("resellercentral", $action, $fields, $data);
    if(strpos($data, "SUCCESS") || strpos($data, "account has been suspended") || strpos($data, "account is now active")) {
        if($creatingaccount) {
            $tempdata = explode("&", $data);
            $tempdata = explode("=", $tempdata[1]);
            $tempdata = explode("<", $tempdata[1]);
            $ipaddress = $tempdata[0];
            $params["model"]->serviceProperties->save(["IP Address" => $ipaddress]);
        }
        $result = "success";
    } elseif(strpos($data, "Account Already Suspended")) {
        $result = "Account Already Suspended";
    } elseif(strpos($data, "a DNS entry for")) {
        $result = "An account already exists for this domain name";
    } else {
        $result = $data;
    }
    return $result;
}

?>