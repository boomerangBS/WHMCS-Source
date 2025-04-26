<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
add_hook("AdminAreaFooterOutput", 1, function (array $vars) {
    $filename = $vars["filename"];
    $return = "";
    if($filename == "clientssummary") {
        $return = "<script type=\"text/javascript\" src=\"https://js.stripe.com/v3/\"></script>";
    }
    return $return;
});
add_hook("ClientAreaFooterOutput", 1, function (array $vars) {
    $filename = $vars["filename"];
    $template = $vars["templatefile"];
    $return = "";
    $requiredFiles = ["cart", "creditcard"];
    $requiredTemplates = ["account-paymentmethods-manage", "invoice-payment"];
    if(in_array($filename, $requiredFiles) || in_array($template, $requiredTemplates)) {
        $return = "<script type=\"text/javascript\" src=\"https://js.stripe.com/v3/\"></script>";
    }
    return $return;
});
Hook::add("AdminHomeWidgets", 1, function () {
    return new WHMCS\Module\Gateway\Stripe\Widget\Stripe();
});
Hook::add("AddGlobalWarnings", 1, function () {
    $warningArray = WHMCS\Module\Gateway\Stripe\Admin\Warning::message("stripe");
    if(!$warningArray) {
        return [];
    }
    return ["stripeWarning" => $warningArray];
});

?>