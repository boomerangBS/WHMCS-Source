<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
function directdebit_MetaData()
{
    return ["gatewayType" => WHMCS\Module\Gateway::GATEWAY_BANK, "failedEmail" => "Direct Debit Payment Failed", "successEmail" => "Direct Debit Payment Confirmation", "pendingEmail" => "Direct Debit Payment Pending", "processingType" => WHMCS\Module\Gateway::PROCESSING_OFFLINE];
}
function directdebit_config()
{
    $configarray = ["FriendlyName" => ["Type" => "System", "Value" => "Direct Debit"]];
    return $configarray;
}
function directdebit_localbankdetails()
{
}

?>