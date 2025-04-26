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
function offlinecc_MetaData()
{
    return ["gatewayType" => WHMCS\Module\Gateway::GATEWAY_CREDIT_CARD, "processingType" => WHMCS\Module\Gateway::PROCESSING_OFFLINE];
}
function offlinecc_config()
{
    return ["FriendlyName" => ["Type" => "System", "Value" => "Offline Credit Card"], "RemoteStorage" => ["Type" => "System", "Value" => true]];
}

?>