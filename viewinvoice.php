<?php

define("CLIENTAREA", true);
require "init.php";
require "includes/gatewayfunctions.php";
require "includes/invoicefunctions.php";
require "includes/clientfunctions.php";
require "includes/adminfunctions.php";
$whmcs = App::self();
$id = $invoiceid = $invoiceIdTitle = (int) $whmcs->get_req_var("id");
$breadcrumbnav = "<a href=\"index.php\">" . $whmcs->get_lang("globalsystemname") . "</a> > <a href=\"clientarea.php\">" . $whmcs->get_lang("clientareatitle") . "</a> > <a href=\"clientarea.php?action=invoices\">" . $_LANG["invoices"] . "</a> > <a href=\"viewinvoice.php?id=" . $invoiceid . "\">" . $_LANG["invoicenumber"] . $invoiceid . "</a>";
$invoice = NULL;
try {
    $invoiceViewHelper = new WHMCS\Invoice($invoiceid);
    $invoice = $invoiceViewHelper->getModel();
} catch (WHMCS\Exception\Module\NotServicable $e) {
    $invoiceViewHelper = new WHMCS\Invoice();
}
$adminUser = WHMCS\User\Admin::getAuthenticatedUser();
if($adminUser && !checkPermission("Manage Invoice", true)) {
    $adminUser = NULL;
}
$existingLanguage = NULL;
if($adminUser && App::getFromRequest("view_as_client") && $invoice) {
    $existingLanguage = getUsersLang($invoice->userid);
}
if($invoice && $invoiceViewHelper->getData("invoicenum")) {
    $invoiceIdTitle = $invoiceViewHelper->getData("invoicenum");
}
$smarty = NULL;
initialiseClientArea($whmcs->get_lang("invoicestitle") . $invoiceIdTitle, "", "", "", $breadcrumbnav);
if(!$adminUser) {
    $exitEarly = function ($smarty) {
        $smarty->assign("error", "on");
        $smarty->assign("invalidInvoiceIdRequested", true);
        outputClientArea("viewinvoice", true);
        exit;
    };
    Auth::requireLoginAndClient(true);
    if(is_null($invoice)) {
        $exitEarly($smarty);
    }
    if($invoice->status == WHMCS\Billing\Invoice::STATUS_DRAFT) {
        $exitEarly($smarty);
    }
    try {
        Auth::forceSwitchClientIdOrFail($invoice->userid);
    } catch (WHMCS\Exception\Authentication\InvalidClientRequested $e) {
        $exitEarly($smarty);
    }
    checkContactPermission("invoices");
}
$smarty->assign("invalidInvoiceIdRequested", false);
if(($invoiceViewHelper->getData("status") === "Paid" || $invoiceViewHelper->getData("status") === "Payment Pending") && isset($_SESSION["orderdetails"]) && $_SESSION["orderdetails"]["InvoiceID"] === $invoiceid && empty($_SESSION["orderdetails"]["paymentcomplete"])) {
    $_SESSION["orderdetails"]["paymentcomplete"] = true;
    redir("a=complete", "cart.php");
}
$gateway = $whmcs->get_req_var("gateway");
if(!empty($gateway) && $invoice instanceof WHMCS\Billing\Invoice && $invoice->paymentmethod !== $gateway) {
    check_token();
    $paymentGatewayOptions = $invoice->paymentGatewayOptionsFactory()->contextClientInvoicePayment(Currency::factoryForClientArea())->make();
    if($paymentGatewayOptions->has($gateway)) {
        $invoice->setPaymentMethod($gateway)->save();
        run_hook("InvoiceChangeGateway", ["invoiceid" => $invoiceid, "paymentmethod" => $gateway]);
    }
    redir("id=" . $invoiceid);
}
$creditbal = get_query_val("tblclients", "credit", ["id" => $invoiceViewHelper->getData("userid")]);
$smartyvalues["manualapplycredit"] = false;
if($invoiceViewHelper->getData("status") == "Unpaid" && 0 < $creditbal && !$invoiceViewHelper->isAddFundsInvoice()) {
    $balance = $invoiceViewHelper->getData("balance");
    $creditamount = $whmcs->get_req_var("creditamount");
    if($whmcs->get_req_var("applycredit") && 0 < $creditamount) {
        check_token();
        if($creditbal < $creditamount) {
            echo $_LANG["invoiceaddcreditovercredit"];
            exit;
        }
        if($balance < $creditamount) {
            echo $_LANG["invoiceaddcreditoverbalance"];
            exit;
        }
        applyCredit($invoiceid, $invoiceViewHelper->getData("userid"), $creditamount);
        redir("id=" . $invoiceid);
    }
    $smartyvalues["manualapplycredit"] = true;
    $clientCurrency = getCurrency($invoiceViewHelper->getData("userid"));
    $smartyvalues["totalcredit"] = formatCurrency($creditbal, $clientCurrency["id"]) . generate_token("form");
    if(!$creditamount) {
        $creditamount = $balance <= $creditbal ? $balance : $creditbal;
    }
    $smartyvalues["creditamount"] = $creditamount;
}
$paymentGatewayOptions = $invoice->paymentGatewayOptionsFactory()->contextClientInvoicePayment(Currency::factoryForClientArea());
$invoice->adjustInvoiceForPaymentGatewayOptions($paymentGatewayOptions);
$invoiceViewHelper = new WHMCS\Invoice($invoice);
$outputvars = $invoiceViewHelper->getOutput();
$smartyvalues = array_merge($smartyvalues, $outputvars);
$invoiceitems = $invoiceViewHelper->getLineItems();
$smartyvalues["invoiceitems"] = $invoiceitems;
$transactions = $invoiceViewHelper->getTransactions();
$smartyvalues["transactions"] = $transactions;
$smartyvalues["paymentbutton"] = "";
if($invoiceViewHelper->getData("status") == "Unpaid" && 0 < $invoiceViewHelper->getData("balance")) {
    $smartyvalues["paymentbutton"] = $invoiceViewHelper->getPaymentLink();
}
$smartyvalues["paymentSuccess"] = (bool) $whmcs->get_req_var("paymentsuccess");
$smartyvalues["paymentInititated"] = (bool) $whmcs->get_req_var("paymentinititated");
$smartyvalues["paymentFailed"] = (bool) $whmcs->get_req_var("paymentfailed");
$smartyvalues["pendingReview"] = (bool) $whmcs->get_req_var("pendingreview");
$smartyvalues["offlineReview"] = (bool) $whmcs->get_req_var("offlinepaid");
$smartyvalues["offlinepaid"] = (bool) $whmcs->get_req_var("offlinepaid");
$smartyvalues["paymentSuccessAwaitingNotification"] = $invoiceViewHelper->showPaymentSuccessAwaitingNotificationMsg($smartyvalues["paymentSuccess"]);
if($whmcs->get_config("AllowCustomerChangeInvoiceGateway")) {
    $smartyvalues["allowchangegateway"] = true;
    $paymentGatewayOptionsList = $paymentGatewayOptions->make()->displayNameMap()->toArray();
    $gatewayDropdownHtml = generate_token("form");
    $gatewayDropdownHtml .= (new WHMCS\Form())->dropdown("gateway", $paymentGatewayOptionsList, $invoice->paymentGateway, "submit()");
    $smartyvalues["gatewaydropdown"] = $gatewayDropdownHtml;
    $smartyvalues["tokenInput"] = generate_token("form");
    $smartyvalues["selectedGateway"] = $invoice->paymentGateway;
    $smartyvalues["availableGateways"] = $paymentGatewayOptionsList;
} else {
    $smartyvalues["allowchangegateway"] = false;
}
$smartyvalues["taxIdLabel"] = Lang::trans(WHMCS\Billing\Tax\Vat::getLabel());
outputClientArea("viewinvoice", true, ["ClientAreaPageViewInvoice"]);
if($existingLanguage) {
    swapLang($existingLanguage);
}

?>