<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
if(!function_exists("moduleConfigFieldOutput")) {
    require_once ROOTDIR . DIRECTORY_SEPARATOR . "includes" . DIRECTORY_SEPARATOR . "modulefunctions.php";
}
$warnings = $mailInterface->environmentCheck();
foreach ($warnings as $warning) {
    echo $warning;
}
foreach ($mailInterface->getConfiguration() as $setting => $configuration) {
    if(!empty($configuration["Type"]) && $configuration["Type"] == "System") {
    } else {
        if(empty($configuration["FriendlyName"])) {
            $configuration["FriendlyName"] = $setting;
        }
        $configuration["Value"] = empty($currentConfiguration["configuration"][$setting]) ? NULL : $currentConfiguration["configuration"][$setting];
        $configuration["Name"] = $setting;
        echo "    <div class=\"form-group mail-provider-configuration\">\n        <label for=\"field";
        echo ucfirst($setting);
        echo "\" class=\"col-md-4 col-sm-6 control-label\">\n            ";
        echo $configuration["FriendlyName"];
        echo "        </label>\n        <div class=\"col-md-8 col-sm-6\">\n            ";
        echo moduleConfigFieldOutput($configuration);
        echo "        </div>\n    </div>\n";
    }
}
if($mailInterface->getSenderInterface() instanceof WHMCS\Module\Contracts\AdminConfigInterface) {
    echo $mailInterface->getSenderInterface()->getExtraAdminConfig();
}

?>