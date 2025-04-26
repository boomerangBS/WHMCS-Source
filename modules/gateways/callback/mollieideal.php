<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
require "../../../init.php";
include_once ROOTDIR . DIRECTORY_SEPARATOR . "modules" . DIRECTORY_SEPARATOR . "gateways" . DIRECTORY_SEPARATOR . "mollieideal" . DIRECTORY_SEPARATOR . "vendor" . DIRECTORY_SEPARATOR . "autoload.php";
$whmcs->load_function("gateway");
$whmcs->load_function("invoice");
$gatewayModule = "mollieideal";
$GATEWAY = getGatewayVariables($gatewayModule);
if(!$GATEWAY["type"]) {
    exit("Module Not Activated");
}
$invoiceId = urldecode($_GET["invoiceid"]);
$transactionAmount = urldecode($_GET["amount"]);
$transactionFee = urldecode($_GET["fee"]);
$transactionId = $_POST["id"];
checkCbTransID($transactionId);
$transactionStatus = "Unsuccessful";
if(isset($transactionId)) {
    try {
        $mollie = new Mollie\Api\MollieApiClient();
        $mollie->setApiKey($GATEWAY["apiKey"]);
        $payment = $mollie->payments->get($_POST["id"]);
    } catch (Mollie\Api\Exceptions\ApiException $e) {
        logTransaction($GATEWAY["paymentmethod"], $_REQUEST, "Payment Could Not Be Confirmed: " . $e->getMessage());
    }
    if($payment->isPaid() && !$payment->hasRefunds() && !$payment->hasChargebacks()) {
        $invoice = WHMCS\Billing\Invoice::findOrFail($invoiceId);
        if(isset($GATEWAY["convertto"]) && 0 < strlen($GATEWAY["convertto"])) {
            $invoiceCurrency = $invoice->getCurrency();
            $invoiceTotal = $invoice->total;
            $transactionAmount = convertCurrency($transactionAmount, $GATEWAY["convertto"], $invoiceCurrency["id"]);
            if($invoiceTotal < $transactionAmount + 1 && $transactionAmount - 1 < $invoiceTotal) {
                $transactionAmount = $invoiceTotal;
            }
        }
        $invoice->addPayment($transactionAmount, $transactionId, $transactionFee, $gatewayModule);
        $transactionStatus = "Successful";
    }
}
logTransaction($GATEWAY["paymentmethod"], $_REQUEST, $transactionStatus);

?>