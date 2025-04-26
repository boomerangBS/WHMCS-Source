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
$moduleType = $whmcs->getFromRequest("moduleType");
$moduleName = $whmcs->getFromRequest("moduleName");
$supportedModuleTypes = ["gateway", "registrar", "addon", "fraud"];
if(!in_array($moduleType, $supportedModuleTypes)) {
    $apiresults = ["result" => "error", "message" => "Invalid module type provided. Supported module types include: " . implode(", ", $supportedModuleTypes)];
} else {
    $moduleClassName = "\\WHMCS\\Module\\" . ucfirst($moduleType);
    $moduleInterface = new $moduleClassName();
    if(!in_array($moduleName, $moduleInterface->getList())) {
        $apiresults = ["result" => "error", "message" => "Invalid module name provided."];
    } else {
        $moduleInterface->load($moduleName);
        try {
            $configurationParams = $moduleInterface->getConfiguration();
            $paramsToReturn = [];
            if(is_array($configurationParams)) {
                foreach ($configurationParams as $key => $values) {
                    if($values["Type"] == "System") {
                        if($key != "FriendlyName") {
                        } else {
                            $values["Type"] = "text";
                        }
                    }
                    $display = $key;
                    if(!empty($values["FriendlyName"])) {
                        $display = $values["FriendlyName"];
                    }
                    $paramsToReturn[] = ["name" => $key, "displayName" => $display, "fieldType" => $values["Type"]];
                }
            }
        } catch (WHMCS\Exception\Module\NotImplemented $e) {
            $apiresults = ["result" => "error", "message" => "Get module configuration parameters not supported by module type."];
            return NULL;
        } catch (Exception $e) {
            $apiresults = ["result" => "error", "message" => "An unexpected error occurred: " . $e->getMessage()];
            return NULL;
        }
        $apiresults = ["result" => "success", "parameters" => $paramsToReturn];
    }
}

?>