<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
add_hook("ClientAreaFooterOutput", 1, function (array $vars) {
    $return = "";
    try {
        WHMCS\Module\Gateway::factory("stripe");
    } catch (Exception $e) {
        $filename = $vars["filename"];
        $template = $vars["templatefile"];
        $requiredFiles = ["cart", "creditcard"];
        $templateFiles = ["account-paymentmethods-manage", "invoice-payment"];
        if(in_array($filename, $requiredFiles) || in_array($template, $templateFiles)) {
            $return = "<script type=\"text/javascript\" src=\"https://js.stripe.com/v3/\"></script>";
        }
    }
    return $return;
});
Hook::add("AddGlobalWarnings", 1, function () {
    $warningArray = WHMCS\Module\Gateway\Stripe\Admin\Warning::message("stripe_sepa");
    if(!$warningArray) {
        return [];
    }
    return ["stripeSepaWarning" => $warningArray];
});

?>