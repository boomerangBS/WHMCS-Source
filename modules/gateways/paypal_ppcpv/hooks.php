<?php

if(!defined("WHMCS")) {
    exit("You cannot access this file directly.");
}
add_hook("ClientAreaHeadOutput", 1, function ($vars) {
    $templatefiles = ["account-paymentmethods-manage", "viewcart", "invoice-payment"];
    if(!isset($vars["templatefile"])) {
        return NULL;
    }
    if(!in_array($vars["templatefile"], $templatefiles)) {
        return NULL;
    }
    if($vars["templatefile"] == "viewcart" && empty($vars["checkout"])) {
        return NULL;
    }
    $module = WHMCS\Module\Gateway\paypal_ppcpv\PayPalCommerce::MODULE_NAME;
    return "<link href=\"" . $vars["WEB_ROOT"] . "/modules/gateways/" . $module . "/css/payment.min.css\" rel=\"stylesheet\" type=\"text/css\"/>";
});

?>