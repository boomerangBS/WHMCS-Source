<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
add_hook("ClientAreaPrimarySidebar", -1, "nominet_HideReleaseDomain");
function nominet_HideReleaseDomain(WHMCS\View\Menu\Item $primarySidebar)
{
    $settingAllowClientTag = get_query_val("tblregistrars", "value", "registrar = 'nominet' AND setting = 'AllowClientTAGChange'");
    $settingAllowClientTag = decrypt($settingAllowClientTag);
    if($settingAllowClientTag == "on") {
        return NULL;
    }
    if(!is_null($primarySidebar->getChild("Domain Details Management"))) {
        $primarySidebar->getChild("Domain Details Management")->removeChild("Release Domain");
    }
}

?>