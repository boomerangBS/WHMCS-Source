<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Module\Registrar\CentralNic;

class CurlCall implements Api\TransportInterface
{
    public function doCall(Commands\AbstractCommand $command, $api) : Commands\AbstractCommand
    {
        $params = $command->getParams();
        $httpMethod = $command->getHttpMethod();
        $postFields = http_build_query(["s_login" => $api->getUsername(), "s_pw" => $api->getPassword(), "s_command" => $api->getParser()->buildPayload($params)]);
        $curlError = NULL;
        $curl = curl_init($api->getUrl());
        $curlArray = [CURLOPT_VERBOSE => false, CURLOPT_CONNECTTIMEOUT => 5000, CURLOPT_TIMEOUT => 300000, CURLOPT_CUSTOMREQUEST => $httpMethod, CURLOPT_POSTFIELDS => $postFields, CURLOPT_HEADER => 0, CURLOPT_RETURNTRANSFER => 1, CURLOPT_HTTPHEADER => ["Expect:", "Content-Type: application/x-www-form-urlencoded", "Content-Length: " . strlen($postFields)]];
        if($api->getCustomHeader()) {
            curl_setopt($curl, CURLOPT_USERAGENT, $api->getCustomHeader());
        }
        if($api->getProxy()) {
            curl_setopt($curl, CURLOPT_PROXY, $api->getProxy());
        }
        curl_setopt_array($curl, $curlArray);
        $curlResponse = curl_exec($curl);
        if($curlResponse === false) {
            $curlResponse = "httperror";
            $curlError = curl_error($curl);
        }
        curl_close($curl);
        logModuleCall("centralnic", $params["command"], urldecode($postFields), $curlResponse, "", [$api->getPassword()]);
        if($curlError) {
            throw new \Exception($curlError);
        }
        return $curlResponse;
    }
}

?>