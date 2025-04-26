<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
add_hook("AdminAreaFooterOutput", 1, function (array $vars) {
    $scriptUrl = "https://secure.ewaypayments.com/scripts/eWAY.min.js";
    $filename = $vars["filename"];
    $return = "";
    if($filename == "clientssummary") {
        $return = "<script src=\"" . $scriptUrl . "\" data-init=\"false\"></script>";
    }
    return $return;
});
add_hook("ClientAreaFooterOutput", 1, function (array $vars) {
    $scriptUrl = "https://secure.ewaypayments.com/scripts/eWAY.min.js";
    $filename = $vars["filename"];
    $template = $vars["templatefile"];
    $return = "";
    $requiredFiles = ["cart"];
    $requiredTemplates = ["account-paymentmethods-manage", "invoice-payment"];
    if(in_array($filename, $requiredFiles) || in_array($template, $requiredTemplates)) {
        $return = "<script src=\"" . $scriptUrl . "\" data-init=\"false\"></script>";
    }
    return $return;
});

?>