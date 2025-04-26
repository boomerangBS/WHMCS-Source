<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Module\Fraud;

class AbstractRequest
{
    protected $licenseKey;
    protected function log($action, $request, $response, $processedResponse)
    {
        $namespace = explode("\\", "WHMCS\\Module\\Fraud");
        $moduleName = end($namespace);
        return logModuleCall(strtolower($moduleName), $action, $request, $response, $processedResponse);
    }
    protected function getClient()
    {
        return new \WHMCS\Http\Client\HttpClient();
    }
}

?>