<?php

define("ADMINAREA", true);
require "../init.php";
$whmcs = App::self();
$action = $whmcs->get_req_var("action");
$warning = $whmcs->get_req_var("warning");
WHMCS\Session::start();
$flashMessage = WHMCS\FlashMessages::get();
WHMCS\Session::release();
if($action == "edit") {
    $reqperm = "Manage Invoice";
} elseif($action == "createinvoice") {
    $reqperm = "Create Invoice";
} else {
    $reqperm = "List Invoices";
}
$aInt = new WHMCS\Admin($reqperm);
$aInt->requiredFiles(["clientfunctions", "invoicefunctions", "gatewayfunctions", "processinvoices", "ccfunctions"]);
$invoiceModel = NULL;
$id = App::getFromRequest("id");
if($action == "edit") {
    $invoice = new WHMCS\Invoice($id);
    $invoiceModel = $invoice->getModel();
    $pageicon = "invoicesedit";
    if($invoice->isProformaInvoice()) {
        $pagetitle = AdminLang::trans("fields.proformaInvoiceNum") . $invoice->getData("invoicenum");
    } else {
        $pagetitle = AdminLang::trans("fields.invoicenum") . $invoice->getData("invoicenum");
    }
} else {
    $pageicon = "invoices";
    $pagetitle = $aInt->lang("invoices", "title");
}
$noPermissionTooltip = function ($permission) {
    return sprintf("aria-disabled=\"true\" data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"%s\" ", addslashes(AdminLang::trans("permissions.missingPerm", [":perm" => AdminLang::trans($permission)])));
};
$aInt->title = $pagetitle;
$aInt->sidebar = "billing";
$aInt->icon = $pageicon;
$invoiceid = (int) $whmcs->get_req_var("invoiceid");
$status = $whmcs->get_req_var("status");
$validInvoiceStatuses = array_merge(WHMCS\Invoices::getInvoiceStatusValues(), ["Overdue", ""]);
if(!in_array($status, $validInvoiceStatuses)) {
    $status = "";
}
if($action == "createinvoice") {
    check_token("WHMCS.admin.default");
    if(!checkActiveGateway()) {
        $aInt->gracefulExit(AdminLang::trans("gateways.nonesetup", [":paymentGatewayURI" => routePath("admin-apps-category", "payments")]));
    }
    $gateway = getClientsPaymentMethod($userid);
    $invoice = WHMCS\Billing\Invoice::newInvoice($userid, $gateway);
    $invoice->save();
    $invoiceid = $invoice->id;
    logActivity("Created Manual Invoice - Invoice ID: " . $invoiceid, $userid);
    $invoice->runCreationHooks("adminarea");
    redir("action=edit&id=" . $invoiceid);
}
$filters = new WHMCS\Filter();
$selectedinvoices = $whmcs->get_req_var("selectedinvoices");
if(!is_array($selectedinvoices)) {
    $selectedinvoices = [];
}
if($whmcs->get_req_var("markpaid")) {
    check_token("WHMCS.admin.default");
    checkPermission("Manage Invoice");
    $failedInvoices = [];
    $invoiceCount = 0;
    foreach ($selectedinvoices as $invid) {
        $invid = (int) $invid;
        if(get_query_val("tblinvoices", "status", ["id" => $invid]) == "Paid") {
        } else {
            $paymentMethod = get_query_val("tblinvoices", "paymentmethod", ["id" => $invid]);
            if(addInvoicePayment($invid, "", "", "", $paymentMethod) === false) {
                $failedInvoices[] = $invid;
            }
            $invoiceCount++;
        }
    }
    if(0 < count($selectedinvoices)) {
        $failedInvoices["successfulInvoicesCount"] = $invoiceCount - count($failedInvoices);
        WHMCS\Cookie::set("FailedMarkPaidInvoices", $failedInvoices);
    }
    $filters->redir();
}
if($whmcs->get_req_var("markunpaid")) {
    check_token("WHMCS.admin.default");
    checkPermission("Manage Invoice");
    foreach ($selectedinvoices as $invid) {
        $invid = (int) $invid;
        $invoice = WHMCS\Billing\Invoice::find($invid);
        $invoice->status = WHMCS\Billing\Invoice::STATUS_UNPAID;
        $invoice->dateCancelled = "0000-00-00 00:00:00";
        $invoice->save();
        logActivity("Reactivated Invoice - Invoice ID: " . $invid, $invoice->clientId);
        run_hook("InvoiceUnpaid", ["invoiceid" => $invid]);
    }
    $filters->redir();
}
if($whmcs->get_req_var("markcancelled")) {
    check_token("WHMCS.admin.default");
    checkPermission("Cancel Invoice");
    foreach ($selectedinvoices as $invid) {
        $invid = (int) $invid;
        $invoice = WHMCS\Billing\Invoice::find($invid);
        $invoice->status = WHMCS\Billing\Invoice::STATUS_CANCELLED;
        $invoice->dateCancelled = WHMCS\Carbon::now();
        $invoice->save();
        logActivity("Cancelled Invoice - Invoice ID: " . $invid, $invoice->clientId);
        run_hook("InvoiceCancelled", ["invoiceid" => $invid]);
    }
    $filters->redir();
}
if($whmcs->get_req_var("duplicateinvoice")) {
    check_token("WHMCS.admin.default");
    checkPermission("Create Invoice");
    foreach ($selectedinvoices as $invid) {
        $invid = (int) $invid;
        $invoices = new WHMCS\Invoices();
        $invoices->duplicate($invid);
    }
    $filters->redir();
}
if($whmcs->get_req_var("massdelete")) {
    check_token("WHMCS.admin.default");
    checkPermission("Delete Invoice");
    foreach ($selectedinvoices as $invid) {
        $invid = (int) $invid;
        $invoice = WHMCS\Billing\Invoice::find($invid);
        $userId = $invoice->clientId;
        $invoice->delete();
        logActivity("Deleted Invoice - Invoice ID: " . $invid, $userId);
    }
    $filters->redir();
}
if($whmcs->get_req_var("paymentreminder")) {
    check_token("WHMCS.admin.default");
    foreach ($selectedinvoices as $invid) {
        $invid = (int) $invid;
        $invoice = WHMCS\Billing\Invoice::find($invid);
        sendMessage("Invoice Payment Reminder", $invid);
        logActivity("Invoice Payment Reminder Sent - Invoice ID: " . $invid, $invoice->clientId);
    }
    $filters->redir();
}
if($whmcs->get_req_var("delete")) {
    check_token("WHMCS.admin.default");
    checkPermission("Delete Invoice");
    $invoiceID = App::getFromRequest("invoiceid");
    try {
        $invoice = WHMCS\Billing\Invoice::findOrFail($invoiceID);
        if($whmcs->get_req_var("returnCredit")) {
            removeCreditOnInvoiceDelete($invoice);
        }
        $userId = $invoice->clientId;
        $invoice->delete();
        logActivity("Deleted Invoice - Invoice ID: " . $invoiceID, $userId);
    } catch (Exception $e) {
    }
    $filters->redir();
}
ob_start();
if($action == "") {
    $name = "invoices";
    $orderby = "duedate";
    $sort = "DESC";
    $pageObj = new WHMCS\Pagination($name, $orderby, $sort);
    $pageObj->digestCookieData();
    $tbl = new WHMCS\ListTable($pageObj, 1, $aInt);
    $tbl->setColumns(["checkall", ["id", AdminLang::trans("fields.invoicenum")], ["clientname", AdminLang::trans("fields.clientname")], ["date", AdminLang::trans("fields.invoicedate")], ["duedate", AdminLang::trans("fields.duedate")], ["last_capture_attempt", AdminLang::trans("fields.lastCaptureAttempt"), "150"], ["total", AdminLang::trans("fields.total")], ["paymentmethod", AdminLang::trans("fields.paymentmethod")], ["status", AdminLang::trans("fields.status")], ["", "&nbsp;", "150"]]);
    $invoicesModel = new WHMCS\Invoices($pageObj);
    if(checkPermission("View Income Totals", true)) {
        $invoicetotals = $invoicesModel->getInvoiceTotals();
        if(count($invoicetotals)) {
            echo "<div class=\"contentbox\" style=\"font-size:18px;\">";
            foreach ($invoicetotals as $vals) {
                echo "<b>" . $vals["currencycode"] . "</b> " . $aInt->lang("status", "paid") . ": <span class=\"textgreen\"><b>" . $vals["paid"] . "</b></span> " . $aInt->lang("status", "unpaid") . ": <span class=\"textred\"><b>" . $vals["unpaid"] . "</b></span> " . $aInt->lang("status", "overdue") . ": <span class=\"textblack\"><b>" . $vals["overdue"] . "</b></span><br />";
            }
            echo "</div><br />";
        }
    }
    echo $aInt->beginAdminTabs([$aInt->lang("global", "searchfilter")]);
    $clientid = $filters->get("clientid");
    $clientid = is_numeric($clientid) ? $clientid : NULL;
    $clientname = $filters->get("clientname");
    $invoicenum = $filters->get("invoicenum");
    $status = $filters->get("status");
    if(!in_array($status, $validInvoiceStatuses)) {
        $status = "";
    }
    echo "\n<!-- Filter -->\n<form action=\"";
    echo $whmcs->getPhpSelf();
    echo "\" method=\"post\">\n\n<table class=\"form\" width=\"100%\" border=\"0\" cellspacing=\"2\" cellpadding=\"3\">\n    <tr>\n        <td width=\"15%\" class=\"fieldlabel\">\n            ";
    echo AdminLang::trans("fields.clientname");
    echo "        </td>\n        <td class=\"fieldarea\">\n            ";
    echo $aInt->clientSearchDropdown("clientid", $clientid, [], "", "id");
    echo "        </td>\n        <td width=\"15%\" class=\"fieldlabel\">\n            ";
    echo AdminLang::trans("fields.invoicedate");
    echo "        </td>\n        <td class=\"fieldarea\">\n            <div class=\"form-group date-picker-prepend-icon\">\n                <label for=\"inputInvoiceDate\" class=\"field-icon\">\n                    <i class=\"fal fa-calendar-alt\"></i>\n                </label>\n                <input id=\"inputInvoiceDate\"\n                       type=\"text\"\n                       name=\"invoicedate\"\n                       value=\"";
    echo $invoicedate = $filters->get("invoicedate");
    echo "\"\n                       class=\"form-control date-picker-search\"\n                       data-opens=\"left\"\n                />\n            </div>\n        </td>\n    </tr>\n    <tr>\n        <td class=\"fieldlabel\">\n            ";
    echo AdminLang::trans("fields.invoicenum");
    echo "        </td>\n        <td class=\"fieldarea\">\n            <input type=\"text\"\n                   name=\"invoicenum\"\n                   class=\"form-control input-150\"\n                   value=\"";
    echo $invoicenum = $filters->get("invoicenum");
    echo "\"\n            >\n        </td>\n        <td width=\"15%\" class=\"fieldlabel\">\n            ";
    echo AdminLang::trans("fields.duedate");
    echo "        </td>\n        <td class=\"fieldarea\">\n            <div class=\"form-group date-picker-prepend-icon\">\n                <label for=\"inputDueDate\" class=\"field-icon\">\n                    <i class=\"fal fa-calendar-alt\"></i>\n                </label>\n                <input id=\"inputDueDate\"\n                       type=\"text\"\n                       name=\"duedate\"\n                       value=\"";
    echo $duedate = $filters->get("duedate");
    echo "\"\n                       class=\"form-control date-picker-search\"\n                       data-opens=\"left\"\n                />\n            </div>\n        </td>\n    </tr>\n    <tr>\n        <td class=\"fieldlabel\">\n            ";
    echo AdminLang::trans("fields.lineitem");
    echo "        </td>\n        <td class=\"fieldarea\">\n            <input type=\"text\"\n                   name=\"lineitem\"\n                   class=\"form-control input-300\"\n                   value=\"";
    echo $lineitem = $filters->get("lineitem");
    echo "\"\n            >\n        </td>\n        <td width=\"15%\" class=\"fieldlabel\">\n            ";
    echo parent::trans("fields.datepaid");
    echo "        </td>\n        <td class=\"fieldarea\">\n            <div class=\"form-group date-picker-prepend-icon\">\n                <label for=\"inputDatePaid\" class=\"field-icon\">\n                    <i class=\"fal fa-calendar-alt\"></i>\n                </label>\n                <input id=\"inputDatePaid\"\n                       type=\"text\"\n                       name=\"datepaid\"\n                       value=\"";
    echo $datepaid = $filters->get("datepaid");
    echo "\"\n                       class=\"form-control date-picker-search\"\n                       data-opens=\"left\"\n                />\n            </div>\n        </td>\n    </tr>\n    <tr>\n        <td class=\"fieldlabel\">\n            ";
    echo AdminLang::trans("fields.paymentmethod");
    echo "        </td>\n        <td class=\"fieldarea\">\n            ";
    $paymentmethod = $filters->get("paymentmethod");
    echo paymentMethodsSelection(AdminLang::trans("global.any"));
    echo "        </td>\n        <td class=\"fieldlabel\">\n            ";
    echo AdminLang::trans("fields.lastCaptureAttempt");
    echo "        </td>\n        <td class=\"fieldarea\">\n            <div class=\"form-group date-picker-prepend-icon\">\n                <label for=\"inputLastCaptureAttempt\" class=\"field-icon\">\n                    <i class=\"fal fa-calendar-alt\"></i>\n                </label>\n                <input id=\"inputLastCaptureAttempt\"\n                       type=\"text\"\n                       name=\"last_capture_attempt\"\n                       value=\"";
    echo $lastCaptureAttempt = $filters->get("last_capture_attempt");
    echo "\"\n                       class=\"form-control date-picker-search\"\n                       data-opens=\"left\"\n                />\n            </div>\n        </td>\n    </tr>\n    <tr>\n        <td class=\"fieldlabel\">\n            ";
    echo AdminLang::trans("fields.status");
    echo "        </td>\n        <td class=\"fieldarea\">\n            <select name=\"status\" class=\"form-control select-inline\">\n                <option value=\"\">\n                    ";
    echo AdminLang::trans("global.any");
    echo "                </option>\n                <option value=\"Draft\"";
    echo $status == "Draft" ? " selected=\"selected\"" : "";
    echo ">\n                    ";
    echo AdminLang::trans("status.draft");
    echo "                </option>\n                <option value=\"Unpaid\"";
    echo $status == "Unpaid" ? " selected=\"selected\"" : "";
    echo ">\n                    ";
    echo AdminLang::trans("status.unpaid");
    echo "                </option>\n                <option value=\"Overdue\"";
    echo $status == "Overdue" ? " selected=\"selected\"" : "";
    echo ">\n                    ";
    echo AdminLang::trans("status.overdue");
    echo "                </option>\n                <option value=\"Paid\"";
    echo $status == "Paid" ? " selected=\"selected\"" : "";
    echo ">\n                    ";
    echo AdminLang::trans("status.paid");
    echo "                </option>\n                <option value=\"Cancelled\"";
    echo $status == "Cancelled" ? " selected=\"selected\"" : "";
    echo ">\n                    ";
    echo AdminLang::trans("status.cancelled");
    echo "                </option>\n                <option value=\"Refunded\"";
    echo $status == "Refunded" ? " selected=\"selected\"" : "";
    echo ">\n                    ";
    echo AdminLang::trans("status.refunded");
    echo "                </option>\n                <option value=\"Collections\"";
    echo $status == "Collections" ? " selected=\"selected\"" : "";
    echo ">\n                    ";
    echo AdminLang::trans("status.collections");
    echo "                </option>\n                <option value=\"Payment Pending\"";
    echo $status == "Payment Pending" ? " selected=\"selected\"" : "";
    echo ">\n                    ";
    echo AdminLang::trans("status.paymentpending");
    echo "                </option>\n            </select>\n        </td>\n        <td class=\"fieldlabel\">\n            ";
    echo AdminLang::trans("fields.dateRefunded");
    echo "        </td>\n        <td class=\"fieldarea\">\n            <div class=\"form-group date-picker-prepend-icon\">\n                <label for=\"inputDateRefunded\" class=\"field-icon\">\n                    <i class=\"fal fa-calendar-alt\"></i>\n                </label>\n                <input id=\"inputDateRefunded\"\n                       type=\"text\"\n                       name=\"date_refunded\"\n                       value=\"";
    echo $dateRefunded = $filters->get("date_refunded");
    echo "\"\n                       class=\"form-control date-picker-search\"\n                       data-opens=\"left\"\n                />\n            </div>\n        </td>\n    </tr>\n    <tr>\n        <td class=\"fieldlabel\">\n            ";
    echo AdminLang::trans("fields.totaldue");
    echo "        </td>\n        <td class=\"fieldarea\">\n            ";
    echo AdminLang::trans("filters.from");
    echo "            <input type=\"number\"\n                   name=\"totalfrom\"\n                   class=\"form-control input-100 input-inline\"\n                   value=\"";
    echo $totalfrom = $filters->get("totalfrom");
    echo "\"\n                   step=\"0.01\"\n            >\n            ";
    echo AdminLang::trans("filters.to");
    echo "            <input type=\"number\"\n                   name=\"totalto\"\n                   class=\"form-control input-100 input-inline\"\n                   value=\"";
    echo $totalto = $filters->get("totalto");
    echo "\"\n                   step=\"0.01\"\n            >\n        </td>\n        <td class=\"fieldlabel\">\n            ";
    echo AdminLang::trans("fields.dateCancelled");
    echo "        </td>\n        <td class=\"fieldarea\">\n            <div class=\"form-group date-picker-prepend-icon\">\n                <label for=\"inputDateCancelled\" class=\"field-icon\">\n                    <i class=\"fal fa-calendar-alt\"></i>\n                </label>\n                <input id=\"inputDateCancelled\"\n                       type=\"text\"\n                       name=\"date_cancelled\"\n                       value=\"";
    echo $dateCancelled = $filters->get("date_cancelled");
    echo "\"\n                       class=\"form-control date-picker-search\"\n                       data-opens=\"left\"\n                />\n            </div>\n        </td>\n    </tr>\n    <tr></tr>\n</table>\n\n<div class=\"btn-container\">\n    <input type=\"submit\" value=\"";
    echo $aInt->lang("global", "search");
    echo "\" class=\"btn btn-default\" />\n</div>\n\n</form>\n\n";
    echo $aInt->endAdminTabs();
    echo "\n<br />\n\n";
    $failedInvoices = WHMCS\Input\Sanitize::makeSafeForOutput(WHMCS\Cookie::get("FailedMarkPaidInvoices", true));
    if(isset($failedInvoices["successfulInvoicesCount"])) {
        $successfulInvoicesCount = (int) $failedInvoices["successfulInvoicesCount"];
        unset($failedInvoices["successfulInvoicesCount"]);
    } else {
        $successfulInvoicesCount = 0;
    }
    WHMCS\Cookie::delete("FailedMarkPaidInvoices");
    if(0 < $successfulInvoicesCount || 0 < count($failedInvoices)) {
        $description = sprintf($aInt->lang("invoices", "markPaidSuccess"), $successfulInvoicesCount);
        if(0 < count($failedInvoices)) {
            $failedInvoicesString = (string) implode(", ", $failedInvoices);
            $description .= "<br />" . sprintf($aInt->lang("invoices", "markPaidError"), $failedInvoicesString);
            $description .= "<br />" . $aInt->lang("invoices", "markPaidErrorInfo") . " <a href=\"https://go.whmcs.com/1857/invoices-tab#bulk-actions\" target=\"_blank\">" . $aInt->lang("global", "findoutmore") . "</a>";
        }
        $infoBoxTitle = $aInt->lang("global", "successWithErrors");
        $infoBoxType = "info";
        if(count($failedInvoices) == 0) {
            $infoBoxTitle = $aInt->lang("global", "success");
            $infoBoxType = "success";
        }
        if($successfulInvoicesCount == 0) {
            $infoBoxTitle = $aInt->lang("global", "erroroccurred");
            $infoBoxType = "error";
        }
        infoBox($infoBoxTitle, $description, $infoBoxType);
        echo $infobox;
    }
    echo WHMCS\View\Asset::jsInclude("jquerytt.js");
    $canCreateInvoice = $aInt->hasPermission("Create Invoice");
    $canCancelInvoice = $aInt->hasPermission("Cancel Invoice");
    $selectors = ["input[name=\"markpaid\"]", "input[name=\"markunpaid\"]", "input[name=\"paymentreminder\"]", "input[name=\"massdelete\"]"];
    $preventors = [];
    if($canCreateInvoice) {
        $selectors[] = "input[name=\"duplicateinvoice\"]";
    } else {
        $preventors[] = "input[name=\"duplicateinvoice\"]";
    }
    if($canCancelInvoice) {
        $selectors[] = "input[name=\"markcancelled\"]";
    } else {
        $preventors[] = "input[name=\"markcancelled\"]";
    }
    $selectors = implode(",", $selectors);
    $preventers = implode(",", $preventors);
    $jqueryCode = "jQuery(\".invtooltip\").invoiceTooltip({cssClass:\"invoicetooltip\"});\n\n\$('" . $selectors . "').on('click', function( event ) {\n    var selectedItems = \$(\"input[name='selectedinvoices[]']\");\n    var name = \$(this).attr('name');\n    switch(name) {\n        case 'markpaid':\n            var langConfirm = '" . $aInt->lang("invoices", "markpaidconfirm", "1") . "';\n            break;\n        case 'markunpaid':\n            var langConfirm = '" . $aInt->lang("invoices", "markunpaidconfirm", "1") . "';\n            break;\n        case 'markcancelled':\n            var langConfirm = '" . $aInt->lang("invoices", "markcancelledconfirm", "1") . "';\n            break;\n        case 'duplicateinvoice':\n            var langConfirm = '" . $aInt->lang("invoices", "duplicateinvoiceconfirm", "1") . "';\n            break;\n        case 'paymentreminder':\n            var langConfirm = '" . $aInt->lang("invoices", "sendreminderconfirm", "1") . "';\n            break;\n        case 'massdelete':\n            var langConfirm = '" . $aInt->lang("invoices", "massdeleteconfirm", "1") . "';\n            break;\n    }\n    if (selectedItems.filter(':checked').length == 0) {\n        event.preventDefault();\n        alert('" . $aInt->lang("global", "pleaseSelectForMassAction") . "');\n    } else {\n        if (!confirm(langConfirm)) {\n            event.preventDefault();\n        }\n    }\n});";
    if($preventers) {
        $jqueryCode .= "\$('" . $preventers . "').on('click', function( event ) {\n    event.preventDefault();\n});";
    }
    $aInt->jquerycode = $jqueryCode;
    $filters->store();
    $criteria = ["clientid" => $clientid, "clientname" => $clientname, "invoicenum" => $invoicenum, "lineitem" => $lineitem, "paymentmethod" => $paymentmethod, "invoicedate" => $invoicedate, "duedate" => $duedate, "datepaid" => $datepaid, "last_capture_attempt" => $lastCaptureAttempt, "date_refunded" => $dateRefunded, "date_cancelled" => $dateCancelled, "totalfrom" => $totalfrom, "totalto" => $totalto, "status" => $status];
    $invoicesModel->execute($criteria);
    $numresults = $pageObj->getNumResults();
    if($filters->isActive() && $numresults == 1) {
        $invoice = $pageObj->getOne();
        $whmcs->redirectToRoutePath("admin-billing-view-invoice", [$invoice["id"]]);
    } else {
        $invoicelist = $pageObj->getData();
        $deleteText = AdminLang::trans("global.delete");
        $editText = AdminLang::trans("global.edit");
        $viewInvoiceText = AdminLang::trans("global.view");
        foreach ($invoicelist as $invoice) {
            $token = generate_token("link");
            $invoiceId = $invoice["id"];
            $credit = $invoice["credit"];
            $payments = WHMCS\Database\Capsule::table("tblaccounts")->where("invoiceid", $invoiceId)->count("id");
            $confirmationModal = "DeleteInvoice";
            if(0 < $credit && 0 < $payments) {
                $confirmationModal = "ExistingCreditAndPayments";
            } elseif(0 < $credit && $payments == 0) {
                $confirmationModal = "ExistingCredit";
            } elseif($credit == 0 && 0 < $payments) {
                $confirmationModal = "ExistingPayments";
            }
            $buttonGroup = [];
            if($aInt->hasPermission("Manage Invoice")) {
                $buttonGroup[] = "<a id=\"viewInvoice" . $invoiceId . "\" " . "href=\"" . $invoice["viewLink"] . "\" class=\"btn btn-default btn-xs\">" . $viewInvoiceText . "</a>";
                $buttonGroup[] = "<a id=\"editInvoice" . $invoiceId . "\" " . "href=\"" . $invoice["editLink"] . "\" class=\"btn btn-default btn-xs\">" . $editText . "</a>";
            } elseif($aInt->hasPermission("View Invoice")) {
                $buttonGroup[] = "<a id=\"viewInvoice" . $invoiceId . "\" " . "href=\"" . $invoice["viewLink"] . "\" class=\"btn btn-default btn-xs\">" . $viewInvoiceText . "</a>";
            }
            if($aInt->hasPermission("Delete Invoice")) {
                $buttonGroup[] = "<button id=\"deleteInvoice" . $invoiceId . "\" type=\"button\" " . "onclick=\"openInvoiceModal('" . $confirmationModal . "', " . $invoice["id"] . ")\"" . " class=\"btn btn-danger btn-xs\">" . $deleteText . "</button>";
            }
            $buttonGroup = implode("", $buttonGroup);
            $tooltipUri = routePath("admin-billing-view-invoice-tooltip", $invoice["id"], generate_token("plain"));
            $total = $invoice["totalformatted"];
            $invoiceTooltipLink = "<a href=\"" . $tooltipUri . "\" class=\"invtooltip\" lang=\"\">" . $total . "</a>";
            $tbl->addRow([["trAttributes" => ["class" => "text-center"], "output" => "<input type='checkbox' name='selectedinvoices[]' value='" . $invoice["id"] . "' class='checkall'>"], "<a href=\"" . $invoice["viewLink"] . "\">" . $invoice["invoicenum"] . "</a>", $invoice["clientname"], $invoice["date"], $invoice["duedate"], $invoice["lastCaptureAttempt"], $invoiceTooltipLink, $invoice["paymentmethod"], $invoice["statusformatted"], "<div class=\"btn-group btn-group-xs\">" . $buttonGroup . "</div>"]);
        }
        $diTooltip = "";
        $diClassDisabled = "";
        $mcClassDisabled = "";
        $mcTooltip = "";
        if(!$canCreateInvoice) {
            $diClassDisabled = " disabled";
            $diTooltip = $noPermissionTooltip("permissions.34");
        }
        if(!$canCancelInvoice) {
            $mcClassDisabled = " disabled";
            $mcTooltip = $noPermissionTooltip("permissions.159");
        }
        $mpButton = $aInt->lang("invoices", "markpaid");
        $mupButton = $aInt->lang("invoices", "markunpaid");
        $mcButton = $aInt->lang("invoices", "markcancelled");
        $diButton = $aInt->lang("invoices", "duplicateinvoice");
        $srButton = $aInt->lang("invoices", "sendreminder");
        $delButton = $aInt->lang("global", "delete");
        $massActionButtons = "<input type=\"submit\" value=\"" . $mpButton . "\" class=\"btn btn-success\" name=\"markpaid\" />\n <input type=\"submit\" value=\"" . $mupButton . "\" class=\"btn btn-default\" name=\"markunpaid\" />\n <input type=\"submit\" value=\"" . $mcButton . "\" class=\"btn btn-default" . $mcClassDisabled . "\" name=\"markcancelled\" " . $mcTooltip . "/>\n <input type=\"submit\" value=\"" . $diButton . "\" class=\"btn btn-default" . $diClassDisabled . "\" name=\"duplicateinvoice\" " . $diTooltip . "/>\n <input type=\"submit\" value=\"" . $srButton . "\" class=\"btn btn-default\" name=\"paymentreminder\" />\n <input type=\"submit\" value=\"" . $delButton . "\" class=\"btn btn-danger\" name=\"massdelete\" />";
        unset($canCancelInvoice);
        unset($canCreateInvoice);
        unset($diClassDisabled);
        unset($diTooltip);
        unset($mcClassDisabled);
        unset($mcTooltip);
        $tbl->setMassActionBtns($massActionButtons);
        echo $tbl->output();
        unset($clientlist);
        unset($invoicesModel);
        $deleteInvoiceTitle = AdminLang::trans("invoices.delete");
        $deleteInvoiceConfirm = AdminLang::trans("invoices.deleteConfirm");
        $deleteInvoiceMessage = "<p>" . $deleteInvoiceTitle . "</p><p>" . $deleteInvoiceConfirm . "</p>";
        echo $aInt->modal("DeleteInvoice", AdminLang::trans("invoices.deleteTitle"), $deleteInvoiceMessage, [["title" => AdminLang::trans("global.delete"), "onclick" => "doDeleteCall()"], ["title" => AdminLang::trans("global.cancel")]]);
        echo $aInt->modal("ExistingCreditAndPayments", $aInt->lang("invoices", "existingCreditTitle"), $aInt->lang("invoices", "existingCredit"), [["title" => $aInt->lang("invoices", "existingCreditReturn"), "onclick" => "\$(\"#modalExistingPaymentsReturnCredit\").modal(\"show\")"], ["title" => $aInt->lang("invoices", "existingCreditDiscard"), "onclick" => "\$(\"#modalExistingPaymentsDiscardCredit\").modal(\"show\");"], ["title" => $aInt->lang("global", "cancel")]]);
        echo $aInt->modal("ExistingPaymentsReturnCredit", $aInt->lang("invoices", "existingPaymentsTitle"), $aInt->lang("invoices", "existingPayments"), [["title" => $aInt->lang("invoices", "existingPaymentsOrphan"), "onclick" => "doDeleteCall(\"returnCredit\");"], ["title" => $aInt->lang("global", "no")]]);
        echo $aInt->modal("ExistingPaymentsDiscardCredit", $aInt->lang("invoices", "existingPaymentsTitle"), $aInt->lang("invoices", "existingPayments"), [["title" => $aInt->lang("invoices", "existingPaymentsOrphan"), "onclick" => "doDeleteCall()"], ["title" => $aInt->lang("global", "no")]]);
        echo $aInt->modal("ExistingCredit", $aInt->lang("invoices", "existingCreditTitle"), $aInt->lang("invoices", "existingCredit"), [["title" => $aInt->lang("invoices", "existingCreditReturn"), "onclick" => "doDeleteCall(\"returnCredit\")"], ["title" => $aInt->lang("invoices", "existingCreditDiscard"), "onclick" => "doDeleteCall()"], ["title" => $aInt->lang("global", "cancel")]]);
        echo $aInt->modal("ExistingPayments", AdminLang::trans("invoices.existingPaymentsTitle"), "<p>" . AdminLang::trans("invoices.delete") . "</p>" . "<p>" . AdminLang::trans("invoices.existingPayments") . "</p>" . "<p>" . AdminLang::trans("invoices.deleteConfirm") . "</p>", [["title" => AdminLang::trans("invoices.existingPaymentsOrphan"), "onclick" => "doDeleteCall()"], ["title" => AdminLang::trans("global.cancel")]]);
        $self = App::getPhpSelf();
        $token = generate_token("link");
        $jscode = "var invoice = 0;\nfunction openInvoiceModal(displayModal, invoiceID)\n{\n    /**\n     * Store the invoiceID in the global JS variable\n     */\n    invoice = invoiceID;\n    \$('#modal' + displayModal).modal('show');\n}\n\nfunction doDeleteCall(credit)\n{\n    var deleteUrl = '" . $self . "?status=" . $status . "&delete=true';\n    if (credit == 'returnCredit') {\n        deleteUrl = deleteUrl + '&returnCredit=true&invoiceid=';\n    } else {\n        deleteUrl = deleteUrl + '&invoiceid=';\n    }\n    window.location = deleteUrl + invoice + '" . $token . "';\n}";
    }
} elseif($action == "edit") {
    $saveoptions = $whmcs->get_req_var("saveoptions");
    $save = $whmcs->get_req_var("save");
    $sub = $whmcs->get_req_var("sub");
    $tplname = $whmcs->get_req_var("tplname");
    $error = $whmcs->get_req_var("error");
    $refundattempted = $whmcs->get_req_var("refundattempted");
    $publishInvoice = $whmcs->get_req_var("publishInvoice");
    $publishAndSendEmail = $whmcs->get_req_var("inputPublishAndSendEmail");
    $reverseCommission = $whmcs->get_req_var("reverseCommission");
    $commissionReversed = $whmcs->get_req_var("commissionReversed");
    $userid = $invoice->getData("userid");
    $oldpaymentmethod = $invoice->getData("paymentmethod");
    $oldInvoiceStatus = $invoice->getData("status");
    $canCancelInvoice = $aInt->hasPermission("Cancel Invoice");
    $missingCancelInvoicePermission = function () {
        WHMCS\Session::start();
        WHMCS\FlashMessages::add(AdminLang::trans("permissions.missingPerm", [":perm" => AdminLang::trans("permissions.159")]), "error");
        WHMCS\Session::release();
    };
    $aInt->assertClientBoundary($userid);
    if($saveoptions) {
        check_token("WHMCS.admin.default");
        $invoice = WHMCS\Billing\Invoice::find($id);
        $invoice->dateCreated = toMySQLDate(App::getFromRequest("invoicedate"));
        $invoice->dateDue = toMySQLDate(App::getFromRequest("datedue"));
        $invoice->setPaymentMethod($paymentmethod);
        $invoice->invoiceNumber = $invoicenum;
        $invoice->taxRate1 = $taxrate;
        $invoice->taxRate2 = $taxrate2;
        if($oldpaymentmethod !== $paymentmethod) {
            $invoice->clearPayMethodId();
        }
        if($oldInvoiceStatus !== $status) {
            switch ($status) {
                case WHMCS\Billing\Invoice::STATUS_REFUNDED:
                    $invoice->setStatusRefunded();
                    break;
                case WHMCS\Billing\Invoice::STATUS_UNPAID:
                    $invoice->setStatusUnpaid();
                    break;
                case WHMCS\Billing\Invoice::STATUS_CANCELLED:
                    if($canCancelInvoice) {
                        $invoice->setStatusCancelled();
                    } else {
                        $missingCancelInvoicePermission();
                    }
                    break;
                case WHMCS\Billing\Invoice::STATUS_PAYMENT_PENDING:
                    $invoice->setStatusPending();
                    break;
                case WHMCS\Billing\Invoice::STATUS_PAID:
                case WHMCS\Billing\Invoice::STATUS_DRAFT:
                case WHMCS\Billing\Invoice::STATUS_COLLECTIONS:
                default:
                    $invoice->status = $status;
            }
        }
        $invoice->save();
        $invoice->updateInvoiceTotal();
        if($oldpaymentmethod != $paymentmethod) {
            run_hook("InvoiceChangeGateway", ["invoiceid" => $id, "paymentmethod" => $paymentmethod]);
        }
        logActivity("Modified Invoice Options - Invoice ID: " . $id, $userid);
        redir("action=edit&id=" . $id);
    }
    if($save == "notes") {
        check_token("WHMCS.admin.default");
        $invoice = WHMCS\Billing\Invoice::find($id);
        $invoice->adminNotes = App::getFromRequest("notes");
        $invoice->save();
        logActivity("Modified Invoice Notes - Invoice ID: " . $id, $userid);
        redir("action=edit&id=" . $id);
    }
    if($sub == "statuscancelled") {
        check_token("WHMCS.admin.default");
        if($canCancelInvoice) {
            $invoice = WHMCS\Billing\Invoice::find($id);
            $invoice->status = WHMCS\Billing\Invoice::STATUS_CANCELLED;
            $invoice->datePaid = "0000-00-00 00:00:00";
            $invoice->dateCancelled = WHMCS\Carbon::now();
            $invoice->save();
            logActivity("Cancelled Invoice - Invoice ID: " . $id, $userid);
            run_hook("InvoiceCancelled", ["invoiceid" => $id]);
        } else {
            $missingCancelInvoicePermission();
        }
        redir("action=edit&id=" . $id);
    }
    if($sub == "statusunpaid") {
        check_token("WHMCS.admin.default");
        $tab = $whmcs->get_req_var("tab");
        $invoice = WHMCS\Billing\Invoice::find($id);
        $invoice->status = WHMCS\Billing\Invoice::STATUS_UNPAID;
        $invoice->datePaid = "0000-00-00 00:00:00";
        $invoice->dateCancelled = "0000-00-00 00:00:00";
        $invoice->dateRefunded = "0000-00-00 00:00:00";
        $invoice->save();
        logActivity("Reactivated Invoice - Invoice ID: " . $id, $userid);
        run_hook("InvoiceUnpaid", ["invoiceid" => $id]);
        if($tab) {
            $tab = "&tab=" . $tab;
        }
        redir("action=edit&id=" . $id . $tab);
    }
    if($sub == "zeroPaid") {
        check_token("WHMCS.admin.default");
        $invoiceStatus = $invoice->getData("status");
        $invoiceBalance = $invoice->getData("balance");
        if($invoiceStatus == "Unpaid" && (int) $invoiceBalance <= 0) {
            processPaidInvoice($id, true);
        }
        redir("action=edit&id=" . $id);
    }
    if($sub == "save") {
        check_token("WHMCS.admin.default");
        $selaction = App::getFromRequest("selaction");
        $items = collect($whmcs->get_req_var("items"));
        $invoice = WHMCS\Billing\Invoice::find($id);
        if(!in_array($selaction, ["delete", "split"])) {
            $itemsToInsert = $items->filter(function ($item) {
                return isset($item["description"]) && trim($item["description"]) !== "" && isset($item["amount"]) && filter_var($item["amount"], FILTER_VALIDATE_FLOAT) !== false;
            });
            $existingInvoiceItemsCollection = $invoice->items;
            foreach ($itemsToInsert as $item) {
                $invoiceItem = isset($item["itemid"]) ? $existingInvoiceItemsCollection->find($item["itemid"]) : NULL;
                if(is_null($invoiceItem)) {
                    $invoiceItem = new WHMCS\Billing\Invoice\Item();
                    $invoiceItem->userId = $invoice->clientId;
                    $invoiceItem->invoiceId = $invoice->id;
                }
                $invoiceItem->description = trim($item["description"]);
                $invoiceItem->amount = $item["amount"];
                $invoiceItem->taxed = $item["taxed"] ?? 0;
                if($invoiceItem->isDirty()) {
                    $invoiceItem->save();
                }
            }
        }
        $batchItems = $items->filter(function ($item) {
            return isset($item["id"]) && filter_var($item["id"], FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]]) !== false;
        });
        if($selaction === "delete" && $batchItems->isNotEmpty()) {
            WHMCS\Database\Capsule::table("tblinvoiceitems")->whereIn("id", $batchItems->pluck("id")->toArray())->where("invoiceid", $id)->delete();
        }
        if($selaction === "split" && $batchItems->isNotEmpty()) {
            $originalInvoice = WHMCS\Billing\Invoice::find($id);
            $totalitemscount = $invoice->items()->count();
            if($batchItems->count() < $totalitemscount) {
                $newInvoice = WHMCS\Billing\Invoice::newInvoice($invoice->clientId, $invoice->paymentGateway, $invoice->taxRate1, $invoice->taxRate2);
                $newInvoice->save();
                $invoiceid = $newInvoice->id;
                $newInvoice->setStatusUnpaid()->save();
                WHMCS\Billing\Invoice\Item::whereIn("id", $batchItems->pluck("id"))->update(["invoiceid" => $newInvoice->id]);
                $newInvoice->updateInvoiceTotal();
                $invoice->updateInvoiceTotal();
                logActivity("Split Invoice - Invoice ID: " . $id . " to Invoice ID: " . $invoiceid, $userid);
                $newInvoice->runCreationHooks("adminarea");
                run_hook("InvoiceSplit", ["originalinvoiceid" => $id, "newinvoiceid" => $invoiceid]);
                redir("action=edit&id=" . $invoiceid);
            }
        }
        $invoice->save();
        $invoice->updateInvoiceTotal();
        $userid = $invoice->clientId;
        logActivity("Modified Invoice - Invoice ID: " . $id, $userid);
        redir("action=edit&id=" . $id);
    }
    $gatewaysarray = getGatewaysArray();
    $data = (array) WHMCS\Database\Capsule::table("tblinvoices")->join("tblclients", "tblclients.id", "=", "tblinvoices.userid")->join("tblpaymentgateways", "tblpaymentgateways.gateway", "=", "tblinvoices.paymentmethod")->where("tblinvoices.id", $id)->where("tblpaymentgateways.setting", "=", "type")->first(["tblinvoices.*", "tblclients.firstname", "tblclients.lastname", "tblclients.companyname", "tblclients.groupid", "tblclients.state", "tblclients.country", "tblpaymentgateways.value"]);
    $paymentmethod = $data["paymentmethod"];
    $type = $data["value"];
    loadGatewayModule($paymentmethod);
    $initiatevscapture = false;
    if(function_exists($paymentmethod . "_initiatepayment")) {
        $initiatevscapture = true;
    }
    if($publishInvoice) {
        check_token("WHMCS.admin.default");
        $invoice = WHMCS\Billing\Invoice::find($id);
        $invoice->status = "Unpaid";
        $invoice->dateCreated = WHMCS\Carbon::now();
        $invoice->save();
        $invoiceArr = ["source" => "adminarea", "user" => WHMCS\Session::get("adminid") ? WHMCS\Session::get("adminid") : "system", "invoiceid" => $id, "status" => "Unpaid"];
        $invoice->runCreationHooks("adminarea");
        logActivity("Modified Invoice Options - Invoice ID: " . $id, $userid);
        if($publishAndSendEmail) {
            run_hook("InvoiceCreationPreEmail", $invoiceArr);
            $emailName = "Invoice Created";
            $paymentMethod = getClientsPaymentMethod($userid);
            if(WHMCS\Module\GatewaySetting::getTypeFor($paymentMethod) === WHMCS\Module\Gateway::GATEWAY_CREDIT_CARD) {
                $emailName = "Credit Card Invoice Created";
            }
            sendMessage($emailName, $id);
        }
        redir("action=edit&id=" . $id);
    }
    if($tplname) {
        check_token("WHMCS.admin.default");
        sendMessage($tplname, $id, [], true);
    }
    if($type == WHMCS\Module\Gateway::GATEWAY_CREDIT_CARD) {
        WHMCS\Session::start();
        $captureStatus = (bool) (int) App::getFromRequest("payment");
        if(App::isInRequest("payment")) {
            $stringPrefix = "capture";
            if($initiatevscapture) {
                $stringPrefix = "initiatepayment";
            }
            $infoBoxTitle = "invoices." . $stringPrefix . "successful";
            $infoBoxDescription = "invoices." . $stringPrefix . "successfulmsg";
            $infoBoxType = "success";
            if(!$captureStatus) {
                $infoBoxTitle = "invoices." . $stringPrefix . "error";
                $infoBoxDescription = "invoices." . $stringPrefix . "errormsg";
                $infoBoxType = "error";
            }
            infoBox(AdminLang::trans($infoBoxTitle), AdminLang::trans($infoBoxDescription), $infoBoxType);
        }
    }
    $transid = App::getFromRequest("transid");
    if($sub == "refund" && $transid) {
        check_token("WHMCS.admin.default");
        checkPermission("Refund Invoice Payments");
        logActivity("Admin Initiated Refund - Invoice ID: " . $id . " - Transaction ID: " . $transid);
        $amount = App::getFromRequest("amount");
        $sendemail = App::getFromRequest("sendemail");
        $refundtransid = App::getFromRequest("refundtransid");
        $refundtype = App::getFromRequest("refundtype");
        $reverse = (bool) (int) App::getFromRequest("reverse");
        $sendtogateway = $addascredit = $commissionReversed = false;
        if($refundtype == "sendtogateway") {
            $sendtogateway = true;
        } elseif($refundtype == "addascredit") {
            $addascredit = true;
        }
        $result = refundInvoicePayment($transid, $amount, $sendtogateway, $addascredit, $sendemail, $refundtransid, $reverse, $reverseCommission, $commissionReversed);
        $queryStr = "";
        if($warning == "removeCredit") {
            $queryStr = "&transid=" . $transid . "&warning=" . $warning . "&invoiceCredit=" . $invoiceCredit;
        }
        if(in_array($result, ["success", "manual"]) && $commissionReversed) {
            $queryStr .= "&commissionReversed=1";
        }
        redir("action=edit&id=" . $id . "&refundattempted=1" . $queryStr . "&refund_result_msg=" . $result);
    }
    if($sub == "deletetrans") {
        check_token("WHMCS.admin.default");
        checkPermission("Delete Transaction");
        $ide = (int) App::getFromRequest("ide");
        $transaction = WHMCS\Billing\Payment\Transaction::find($ide);
        $userId = $transaction->clientId;
        $transaction->delete();
        logActivity("Deleted Transaction - Transaction ID: " . $ide, $userId);
        redir("action=edit&id=" . $id);
    }
    $jscode = "function showrefundtransid() {\n    var refundtype = \$(\"#refundtype\").val();\n    if (refundtype != \"\") {\n        \$(\"#refundtransid\").slideUp();\n    } else {\n        \$(\"#refundtransid\").slideDown();\n    }\n}";
    if($refundattempted) {
        $refundSuccess = true;
        $refundResultMsg = App::getFromRequest("refund_result_msg");
        $infoBoxTitle = $infoBoxDescription = "";
        switch ($refundResultMsg) {
            case "manual":
                $infoBoxTitle = AdminLang::trans("invoices.refundsuccess");
                $infoBoxDescription = AdminLang::trans("invoices.refundmanualsuccessmsg");
                break;
            case "success":
                $infoBoxTitle = AdminLang::trans("invoices.refundsuccess");
                $infoBoxDescription = AdminLang::trans("invoices.refundsuccessmsg");
                break;
            case "creditsuccess":
                $infoBoxTitle = AdminLang::trans("invoices.refundsuccess");
                $infoBoxDescription = AdminLang::trans("invoices.refundcreditmsg");
                break;
            case "amounterror":
            default:
                $refundSuccess = false;
                $infoBoxTitle = AdminLang::trans("invoices.refundfailed");
                $infoBoxDescription = AdminLang::trans("invoices.refundfailedmsg");
                if($refundSuccess && $warning == "removeCredit") {
                    removeOverpaymentCredit($userid, $transid, $invoiceCredit);
                }
                if($commissionReversed) {
                    $infoBoxDescription .= "<br/>" . AdminLang::trans("affiliates.reverseCommissionSuccess");
                }
                infoBox($infoBoxTitle, $infoBoxDescription);
                unset($infoBoxTitle);
                unset($infoBoxDescription);
        }
    }
    $failedData = [];
    if($error == "validation") {
        $repopulateData = WHMCS\Cookie::get("ValidationError", true);
        $errorMessage = "";
        foreach ($repopulateData["validationError"] as $validationError) {
            $errorMessage .= WHMCS\Input\Sanitize::makeSafeForOutput($validationError) . "<br />";
        }
        if($errorMessage) {
            infobox($aInt->lang("global", "validationerror"), $errorMessage, "error");
        }
        $failedData = $repopulateData["submission"];
        WHMCS\Cookie::delete("ValidationError");
    }
    if(!empty($flashMessage)) {
        echo WHMCS\View\Helper::alert($flashMessage["text"], $flashMessage["type"] === "error" ? "danger" : $flashMessage["type"]);
    }
    echo $infobox;
    $id = $data["id"];
    $invoicenum = $data["invoicenum"];
    $date = $data["date"];
    $duedate = $data["duedate"];
    $datepaid = $data["datepaid"];
    $subtotal = $data["subtotal"];
    $credit = $data["credit"];
    $tax = $data["tax"];
    $tax2 = $data["tax2"];
    $total = $data["total"];
    $taxrate = $data["taxrate"];
    $taxrate2 = $data["taxrate2"];
    if(round($taxrate, 2) == $taxrate) {
        $taxrate = format_as_currency($taxrate);
    }
    if(round($taxrate2, 2) == $taxrate2) {
        $taxrate2 = format_as_currency($taxrate2);
    }
    $status = $data["status"];
    $paymentmethod = $data["paymentmethod"];
    $payMethodId = $data["paymethodid"];
    $notes = $data["notes"];
    $userid = $data["userid"];
    $firstname = $data["firstname"];
    $lastname = $data["lastname"];
    $companyname = $data["companyname"];
    $groupid = $data["groupid"];
    $clientstate = $data["state"];
    $clientcountry = $data["country"];
    $date = fromMySQLDate($date);
    $duedate = fromMySQLDate($duedate);
    $datepaid = fromMySQLDate($datepaid, "time");
    $lastCaptureAttempt = $invoice->getData("last_capture_attempt");
    $dateRefunded = $invoice->getData("date_refunded");
    $dateCancelled = $invoice->getData("date_cancelled");
    $payMethod = NULL;
    if($payMethodId) {
        $payMethod = WHMCS\Payment\PayMethod\Model::find($payMethodId);
    }
    if(!$id) {
        $aInt->gracefulExit("Invoice ID Not Found");
    }
    $currency = getCurrency($userid);
    $result = select_query("tblaccounts", "COUNT(id),SUM(amountin)-SUM(amountout)", ["invoiceid" => $id]);
    $data = mysql_fetch_array($result);
    list($transcount, $amountpaid) = $data;
    $balance = $total - $amountpaid;
    $balance = $rawbalance = format_as_currency($balance);
    if($status == "Unpaid") {
        $paymentmethodfriendly = $gatewaysarray[$paymentmethod];
    } elseif($transcount == 0) {
        $paymentmethodfriendly = $aInt->lang("invoices", "notransapplied");
    } else {
        $paymentmethodfriendly = $gatewaysarray[$paymentmethod];
    }
    if(0 < $credit) {
        if($total == 0) {
            $paymentmethodfriendly = $aInt->lang("invoices", "fullypaidcredit");
        } else {
            $paymentmethodfriendly .= " + " . $aInt->lang("invoices", "partialcredit");
        }
    }
    $initiatevscapture = function_exists($paymentmethod . "_initiatepayment") ? true : false;
    $paymentGateways = new WHMCS\Module\Gateway();
    if($paymentGateways->load($paymentmethod)) {
        $gatewayParams = getGatewayVariables($paymentmethod, $id);
        if(App::isInRequest("cancelpayment") && $paymentGateways->functionExists("cancel_payment")) {
            $historyId = (int) App::getFromRequest("cancelpayment");
            if($historyId) {
                $payment = WHMCS\Billing\Payment\Transaction\History::find($historyId);
                if($payment && $payment->invoiceId == $id) {
                    $gatewayParams["history"] = $payment;
                    $gatewayParams["cancelTransactionId"] = $payment->transactionId;
                    $response = $paymentGateways->call("cancel_payment", $gatewayParams);
                    if($response && is_array($response)) {
                        echo WHMCS\View\Helper::alert($response["msg"], $response["type"]);
                        logTransaction($gatewayParams["paymentmethod"], $response["rawdata"], $response["status"]);
                    }
                    unset($gatewayParams["cancelTransactionId"]);
                    unset($gatewayParams["history"]);
                }
            }
        }
        if($paymentGateways->functionExists("adminstatusmsg")) {
            $response = $paymentGateways->call("adminstatusmsg", array_merge(["invoiceid" => $id, "userid" => $userid, "date" => $date, "duedate" => $duedate, "datepaid" => $datepaid, "subtotal" => $subtotal, "tax" => $tax, "tax2" => $tax2, "total" => $total, "status" => $status], $gatewayParams));
            if($response && is_array($response) && array_key_exists("msg", $response)) {
                infoBox($response["title"], $response["msg"], $response["type"]);
                echo $infobox;
            } elseif($response && is_array($response) && array_key_exists("alert", $response)) {
                echo WHMCS\View\Helper::alert($response["alertText"], $response["type"]);
            }
        }
    }
    if($status == "Draft") {
        echo WHMCS\View\Helper::alert(AdminLang::trans("invoices.draftInvoiceNotice"), "info");
    }
    echo "\n<div id=\"unsavedChangesAlert\" class=\"hidden invoice-alert-bottom alert-warning\">\n    <i class=\"far fa-info-circle\"></i>\n    ";
    echo AdminLang::trans("invoices.unsavedChanges");
    echo "</div>\n\n";
    $aInt->deleteJSConfirm("doDeleteTransaction", "invoices", "deletetransaction", "?action=edit&id=" . $id . "&sub=deletetrans&ide=");
    run_hook("ViewInvoiceDetailsPage", ["invoiceid" => $id]);
    $downloadUrl = WHMCS\Utility\Environment\WebHelper::getBaseUrl() . "/dl.php?type=i&id=" . $id;
    $printUrl = $downloadUrl . "&viewpdf=1";
    $langParam = "&language=" . AdminLang::getName();
    $clientInvoiceLink = WHMCS\Utility\Environment\WebHelper::getBaseUrl() . "/viewinvoice.php?id=" . $id . "&view_as_client=1";
    $adminLanguage = ucfirst(AdminLang::getName());
    $clientLang = ucfirst(Lang::getValidLanguageName($invoice->getModel()->client->language ?: Lang::getDefault()));
    echo "\n<div class=\"pull-right-md-larger\">\n    <a id=\"btnViewInvoice\"\n       class=\"btn btn-default btn-sm\"\n       href=\"";
    echo fqdnRoutePath("admin-billing-view-invoice", $invoiceModel->id);
    echo "\"\n    >\n        <i aria-hidden=\"true\" class=\"fas fa-eye fa-fw\"></i>\n        ";
    echo AdminLang::trans("invoices.viewInvoice");
    echo "    </a>\n    <div class=\"btn-group btn-group-sm\" role=\"group\">\n        <button id=\"viewInvoiceAsClientButton\" type=\"button\" class=\"btn btn-default\" onclick=\"window.open('";
    echo $clientInvoiceLink;
    echo "','clientInvoice','')\">\n            <i class=\"fas fa-clipboard\"></i> ";
    echo AdminLang::trans("invoices.viewAsClient");
    echo "        </button>\n\n        <div class=\"btn-group btn-group-sm\">\n            <button type=\"button\" class=\"btn btn-default dropdown-menu-left dropdown-toggle\" data-toggle=\"dropdown\" aria-haspopup=\"true\" aria-expanded=\"false\">\n                <i class=\"fas fa-print\"></i> ";
    echo AdminLang::trans("invoices.viewpdf");
    echo " <span class=\"caret\"></span>\n            </button>\n            <ul class=\"dropdown-menu\">\n                <li>\n                    <a href=\"#\" onclick=\"window.open('";
    echo $printUrl;
    echo "','pdfinv',''); return false;\">\n                        ";
    echo AdminLang::trans("invoices.printAs", [":type" => AdminLang::trans("fields.client"), ":lang" => $clientLang]);
    echo "                    </a>\n                </li>\n                <li>\n                    <a href=\"#\" onclick=\"window.open('";
    echo $printUrl . $langParam;
    echo "','pdfinv',''); return false;\">\n                        ";
    echo AdminLang::trans("invoices.printAs", [":type" => AdminLang::trans("fields.admin"), ":lang" => $adminLanguage]);
    echo "                    </a>\n                </li>\n            </ul>\n        </div>\n\n        <div class=\"btn-group btn-group-sm\">\n            <button type=\"button\" class=\"btn btn-default dropdown-toggle\" data-toggle=\"dropdown\" aria-haspopup=\"true\" aria-expanded=\"false\">\n                <i class=\"fas fa-download\"></i> ";
    echo AdminLang::trans("invoices.downloadpdf");
    echo " <span class=\"caret\"></span>\n            </button>\n            <ul class=\"dropdown-menu dropdown-menu-right\">\n                <li>\n                    <a href=\"";
    echo $downloadUrl;
    echo "\">\n                        ";
    echo AdminLang::trans("invoices.downloadAs", [":type" => AdminLang::trans("fields.client"), ":lang" => $clientLang]);
    echo "                    </a>\n                </li>\n                <li>\n                    <a href=\"";
    echo $downloadUrl . $langParam;
    echo "\">\n                        ";
    echo AdminLang::trans("invoices.downloadAs", [":type" => AdminLang::trans("fields.admin"), ":lang" => $adminLanguage]);
    echo "                    </a>\n                </li>\n            </ul>\n        </div>\n    </div>\n</div>\n<br />\n\n";
    echo $aInt->beginAdminTabs([$aInt->lang("invoices", "summary"), $aInt->lang("invoices", "addpayment"), $aInt->lang("invoices", "options"), $aInt->lang("fields", "credit"), $aInt->lang("invoices", "refund"), $aInt->lang("fields", "notes")], true);
    if($status == "Draft") {
        echo "<div class=\"context-btn-container\">\n    <form method=\"post\" action=\"invoices.php?action=edit&id=";
        echo $id;
        echo "\">\n        <input type=\"hidden\" name=\"publishInvoice\" value=\"1\">\n        <input type=\"submit\" id=\"inputPublish\" name=\"inputPublish\" value=\"";
        echo $aInt->lang("invoices", "publish");
        echo "\" class=\"btn btn-primary\">\n        <input type=\"submit\" id=\"inputPublishAndSendEmail\" name=\"inputPublishAndSendEmail\" value=\"";
        echo $aInt->lang("invoices", "publishAndSendEmail");
        echo "\" class=\"btn btn-warning\" />\n    </form>\n</div>\n";
    }
    echo "<div class=\"row\">\n    <div class=\"col-md-6 col-sm-12\">\n        <table class=\"form\" width=\"100%\">\n            <tr>\n                <td width=\"35%\" class=\"fieldlabel\">\n                    ";
    echo AdminLang::trans("fields.clientname");
    echo "                </td>\n                <td class=\"fieldarea\">\n                    ";
    echo $aInt->outputClientLink($userid, $firstname, $lastname, $companyname, $groupid);
    echo "                    (<a href=\"clientsinvoices.php?userid=";
    echo $userid;
    echo "\">\n                        ";
    echo AdminLang::trans("invoices.viewinvoices");
    echo "                    </a>)\n                </td>\n            </tr>\n            ";
    if($invoicenum) {
        echo "                <tr>\n                    <td class=\"fieldlabel\">\n                        ";
        echo AdminLang::trans("fields.invoicenum");
        echo "                    </td>\n                    <td class=\"fieldarea\">\n                        ";
        echo $invoicenum;
        echo "                    </td>\n                </tr>\n            ";
    }
    echo "            <tr>\n                <td class=\"fieldlabel\">\n                    ";
    echo AdminLang::trans("fields.invoicedate");
    echo "                </td>\n                <td class=\"fieldarea\">\n                    ";
    echo $date;
    echo "                </td>\n            </tr>\n            <tr>\n                <td class=\"fieldlabel\">\n                    ";
    echo AdminLang::trans("fields.duedate");
    echo "                </td><td class=\"fieldarea\">";
    echo $duedate;
    echo "</td></tr>\n            <tr>\n                <td class=\"fieldlabel\">\n                    ";
    echo AdminLang::trans("fields.invoiceamount");
    echo "                </td>\n                <td class=\"fieldarea\" id=\"summaryInvoiceAmount\">\n                    ";
    echo formatCurrency($credit + $total);
    echo "                </td>\n            </tr>\n            <tr>\n                <td class=\"fieldlabel\">\n                    ";
    echo AdminLang::trans("fields.balance");
    echo "                </td>\n                <td class=\"fieldarea\">\n                    <span style=\"font-weight: bold; color: ";
    echo 0 < $rawbalance ? "#cc0000" : "#99cc00";
    echo ";\" id=\"summaryBalance\">\n                        ";
    echo formatCurrency($balance);
    echo "                    </span>\n                </td>\n            </tr>\n        </table>\n    </div>\n    <div class=\"col-md-6 col-sm-12 text-center\">\n        ";
    if($status == WHMCS\Billing\Invoice::STATUS_DRAFT) {
        echo "            <span class=\"textgrey\" style=\"font-family:Arial;font-size:20px;font-weight:bold;text-transform:uppercase\">\n                ";
        echo AdminLang::trans("status.draft");
        echo "            </span>\n        ";
    } elseif($status == WHMCS\Billing\Invoice::STATUS_UNPAID) {
        echo "            <span class=\"textred\" style=\"font-family:Arial;font-size:20px;font-weight:bold;text-transform:uppercase\">\n                ";
        echo AdminLang::trans("status.unpaid");
        echo "            </span>\n            ";
        if($type == WHMCS\Module\Gateway::GATEWAY_CREDIT_CARD) {
            echo "<br />" . AdminLang::trans("fields.lastCaptureAttempt") . ": <b>" . ($lastCaptureAttempt != "0000-00-00 00:00:00" ? fromMySQLDate($lastCaptureAttempt, true) : AdminLang::trans("global.none")) . "</b>";
        }
        echo "        ";
    } elseif($status == WHMCS\Billing\Invoice::STATUS_PAID) {
        echo "            <span class=\"textgreen\" style=\"font-family:Arial;font-size:20px;font-weight:bold;text-transform:uppercase\">\n                ";
        echo AdminLang::trans("status.paid");
        echo "            </span>\n            <br><b>";
        echo $datepaid;
        echo "</b>\n        ";
    } elseif($status == WHMCS\Billing\Invoice::STATUS_CANCELLED) {
        echo "            <span class=\"textgrey\" style=\"font-family:Arial;font-size:20px;font-weight:bold;text-transform:uppercase\">\n                ";
        echo AdminLang::trans("status.cancelled");
        echo "            </span>\n        ";
    } elseif($status == WHMCS\Billing\Invoice::STATUS_REFUNDED) {
        echo "            <span class=\"textblue\" style=\"font-family:Arial;font-size:20px;font-weight:bold;text-transform:uppercase\">\n                ";
        echo AdminLang::trans("status.refunded");
        echo "            </span>\n        ";
    } elseif($status == WHMCS\Billing\Invoice::STATUS_COLLECTIONS) {
        echo "            <span class=\"textgold\" style=\"font-family:Arial;font-size:20px;font-weight:bold;text-transform:uppercase\">\n                ";
        echo AdminLang::trans("status.collections");
        echo "            </span>\n        ";
    } elseif($status == WHMCS\Billing\Invoice::STATUS_PAYMENT_PENDING) {
        echo "            <span class=\"textgreen\" style=\"font-family:Arial;font-size:20px;font-weight:bold;text-transform:uppercase\">\n                ";
        echo AdminLang::trans("status.paymentpending");
        echo "            </span>\n        ";
    }
    echo "        <br>\n        ";
    echo AdminLang::trans("fields.paymentmethod");
    echo ":\n        <strong>";
    echo $paymentmethodfriendly;
    echo "</strong>\n        ";
    if($payMethod) {
        $payMethodGateway = $payMethod->getGateway();
        if($payMethodGateway && $payMethodGateway->getDisplayName() === $paymentmethodfriendly) {
            echo " - " . $payMethod->payment->getDisplayName();
        }
    }
    echo "        <br/><img src=\"images/spacer.gif\" width=\"1\" height=\"10\"/><br/>\n        <form method=\"post\" action=\"invoices.php?action=edit&id=";
    echo $id;
    echo "\"\n              class=\"bottom-margin-5\">\n            <select name=\"tplname\" class=\"form-control select-inline\">";
    $emailtplsarray = [];
    $invoiceMailTemplates = WHMCS\Mail\Template::where("type", "=", "invoice")->where("language", "=", "")->get();
    foreach ($invoiceMailTemplates as $template) {
        $emailtplsarray[$template->name] = $template->id;
    }
    $emailtplsoutput = ["Invoice Created", "Credit Card Invoice Created", "Invoice Payment Reminder", "First Invoice Overdue Notice", "Second Invoice Overdue Notice", "Third Invoice Overdue Notice", "Credit Card Payment Due", "Credit Card Payment Failed", "Invoice Payment Confirmation", "Credit Card Payment Confirmation", "Invoice Refund Confirmation"];
    if($status == WHMCS\Billing\Invoice::STATUS_PAID) {
        $emailtplsoutput = array_merge(["Invoice Payment Confirmation", "Credit Card Payment Confirmation"], $emailtplsoutput);
    }
    if($status == WHMCS\Billing\Invoice::STATUS_REFUNDED) {
        $emailtplsoutput = array_merge(["Invoice Refund Confirmation"], $emailtplsoutput);
    }
    foreach ($emailtplsoutput as $tplname) {
        if(array_key_exists($tplname, $emailtplsarray)) {
            echo "<option>" . $tplname . "</option>";
            unset($emailtplsarray[$tplname]);
        }
    }
    foreach ($emailtplsarray as $tplname => $k) {
        echo "<option>" . $tplname . "</option>";
    }
    echo "            </select>\n            ";
    $captureButtonText = AdminLang::trans("invoices.attemptcapture");
    $captureDisabled = "";
    if($initiatevscapture) {
        $captureButtonText = AdminLang::trans("invoices.initiatepayment");
    }
    if(in_array($status, [WHMCS\Billing\Invoice::STATUS_PAID, WHMCS\Billing\Invoice::STATUS_CANCELLED, WHMCS\Billing\Invoice::STATUS_DRAFT]) || !function_exists($paymentmethod . "_capture") || $paymentmethod === "offlinecc") {
        $captureDisabled = " disabled=\"disabled\"";
    }
    $hasPayMethods = false;
    try {
        if($invoiceModel instanceof WHMCS\Billing\Invoice) {
            $hasPayMethods = 0 < $invoiceModel->client->payMethods->count();
        }
    } catch (Exception $e) {
    }
    $self = App::getPhpSelf();
    $token = generate_token("link");
    echo "            <button type=\"submit\"\n                    id=\"btnSendEmail\"\n                   class=\"btn btn-default";
    echo $status == WHMCS\Billing\Invoice::STATUS_DRAFT ? " disabled" : "";
    echo "\"\n                   ";
    echo $status == WHMCS\Billing\Invoice::STATUS_DRAFT ? "disabled=\"disabled\"" : "";
    echo "            >\n                ";
    echo AdminLang::trans("global.sendemail");
    echo "            </button>\n        </form>\n        <a href=\"";
    echo routePath("admin-client-invoice-capture", $userid, $id);
    echo "\"\n           class=\"btn btn-success open-modal\"";
    echo $captureDisabled;
    echo "           id=\"btnShowAttemptCaptureDialog\"\n           data-btn-submit-id=\"btnAttemptCapture\"\n           data-btn-submit-label=\"";
    echo $captureButtonText;
    echo "\"\n           data-modal-title=\"";
    echo $captureButtonText;
    echo "\"\n        >\n            ";
    echo $captureButtonText;
    echo "        </a>\n        ";
    $mcClassDisabled = "";
    $mcDisabledAttribute = "";
    $mcJavascript = "";
    if(!$canCancelInvoice || $status == WHMCS\Billing\Invoice::STATUS_CANCELLED) {
        $mcClassDisabled = " disabled";
        $mcDisabledAttribute = "disabled=\"disabled\"";
        $mcJavascript = $noPermissionTooltip("permissions.159");
    }
    echo "        <button id=\"btnMarkCancelled\"\n                type=\"button\"\n                class=\"button btn btn-default";
    echo $mcClassDisabled;
    echo "\"\n                data-url=\"";
    echo $self;
    echo "?action=edit&id=";
    echo $id;
    echo "&sub=statuscancelled";
    echo $token;
    echo "\"\n                ";
    echo $mcDisabledAttribute;
    echo "                ";
    echo $mcJavascript;
    echo "        >\n            ";
    echo AdminLang::trans("invoices.markcancelled");
    echo "        </button>\n        ";
    unset($mcClassDisabled);
    unset($mcDisabledAttribute);
    unset($mcJavascript);
    $invoiceStatus = $invoice->getData("status");
    $invoiceBalance = $invoice->getData("balance");
    if($invoiceStatus == WHMCS\Billing\Invoice::STATUS_UNPAID && (int) $invoiceBalance <= 0) {
        echo "            <button id=\"btnMarkPaid\"\n                    type=\"button\"\n                    data-url=\"";
        echo $self;
        echo "?action=edit&id=";
        echo $id;
        echo "&sub=zeroPaid";
        echo $token;
        echo "\"\n                    class=\"button btn btn-info\"\n                    data-toggle=\"tooltip\"\n                    data-placement=\"left\"\n                    title=\"";
        echo AdminLang::trans("invoices.zeroPaid");
        echo "\"\n            >\n                ";
        echo AdminLang::trans("invoices.markpaid");
        echo "            </button>\n            ";
    } else {
        echo "            <button type=\"button\"\n                    id=\"btnMarkUnpaid\"\n                    data-url=\"";
        echo $self;
        echo "?action=edit&id=";
        echo $id;
        echo "&sub=statusunpaid";
        echo $token;
        echo "\"\n                    class=\"button btn btn-default\"\n                ";
        echo $status == WHMCS\Billing\Invoice::STATUS_UNPAID ? "disabled=\"disabled\"" : "";
        echo "            >\n                ";
        echo AdminLang::trans("invoices.markunpaid");
        echo "            </button>\n            ";
    }
    echo "\n        ";
    $addons_html = run_hook("AdminInvoicesControlsOutput", ["invoiceid" => $id, "userid" => $userid, "subtotal" => $subtotal, "tax" => $tax, "tax2" => $tax2, "credit" => $credit, "total" => $total, "balance" => $balance, "taxrate" => $taxrate, "taxrate2" => $taxrate2, "paymentmethod" => $paymentmethod]);
    foreach ($addons_html as $output) {
        echo $output;
    }
    echo "    </div>\n</div>\n\n";
    echo $aInt->nextAdminTab();
    if($status != WHMCS\Billing\Invoice::STATUS_CANCELLED && $status != WHMCS\Billing\Invoice::STATUS_DRAFT) {
        $duplicateTransactionModal = $aInt->modal("DuplicateTransaction", AdminLang::trans("transactions.duplicateTransaction"), AdminLang::trans("transactions.forceDuplicateTransaction"), [["title" => AdminLang::trans("global.continue"), "onclick" => "addInvoicePayment();return false;", "class" => "btn-danger"], ["title" => AdminLang::trans("global.cancel"), "onclick" => "cancelAddPayment();return false;"]]);
        echo "    <form method=\"post\" id=\"addPayment\" action=\"";
        echo $whmcs->getPhpSelf();
        echo "\">\n    <input type=\"hidden\" name=\"action\" value=\"edit\">\n    <input type=\"hidden\" name=\"id\" value=\"";
        echo $id;
        echo "\" id=\"invoiceId\">\n    <input type=\"hidden\" name=\"sub\" value=\"markpaid\">\n\n    ";
        if(0 < $total && $rawbalance <= 0) {
            infoBox($aInt->lang("invoices", "paidstatuscredit"), $aInt->lang("invoices", "paidstatuscreditdesc"));
            echo $infobox;
        }
        if($failedData) {
            $paymentmethod = $failedData["paymentmethod"];
        }
        $paymentMethodDropDown = paymentMethodsSelection($aInt->lang("global", "none"));
        $addPaymentDate = $failedData ? $failedData["date"] : getTodaysDate();
        $addPaymentBalance = $failedData ? $failedData["amount"] : $rawbalance;
        $addPaymentFees = $failedData ? $failedData["fees"] : "0.00";
        $addPaymentTransId = $failedData ? $failedData["transid"] : "";
        $addPaymentSendConfirmationChecked = !$failedData || $failedData["sendconfirmation"] ? " checked " : "";
        echo "    <table class=\"form\" width=\"100%\" border=\"0\" cellspacing=\"2\" cellpadding=\"3\">\n        <tr>\n            <td width=\"20%\" class=\"fieldlabel\">\n                " . $aInt->lang("fields", "date") . "\n            </td>\n            <td class=\"fieldarea\">\n                <div class=\"form-group date-picker-prepend-icon\">\n                    <label for=\"inputDate\" class=\"field-icon\">\n                        <i class=\"fal fa-calendar-alt\"></i>\n                    </label>\n                    <input id=\"inputDate\"\n                           type=\"text\"\n                           name=\"date\"\n                           value=\"" . $addPaymentDate . "\"\n                           class=\"form-control date-picker-single\"\n                    />\n                </div>\n            </td>\n            <td width=\"20%\" class=\"fieldlabel\">\n                " . $aInt->lang("fields", "amount") . "\n            </td>\n            <td class=\"fieldarea\">\n                <input type=\"text\" name=\"amount\" value=\"" . $addPaymentBalance . "\" class=\"form-control input-150\">\n            </td>\n        </tr>\n        <tr>\n            <td class=\"fieldlabel\">\n                " . $aInt->lang("fields", "paymentmethod") . "\n            </td>\n            <td class=\"fieldarea\">\n                " . $paymentMethodDropDown . "\n            </td>\n            <td class=\"fieldlabel\">\n                " . $aInt->lang("fields", "fees") . "\n            </td>\n            <td class=\"fieldarea\">\n                <input type=\"text\" name=\"fees\" value=\"" . $addPaymentFees . "\" class=\"form-control input-150\">\n            </td>\n        </tr>\n        <tr>\n            <td class=\"fieldlabel\">\n                " . $aInt->lang("fields", "transid") . "\n            </td>\n            <td class=\"fieldarea\">\n                <input type=\"text\" name=\"transid\" value=\"" . $addPaymentTransId . "\" class=\"form-control input-250\">\n            </td>\n            <td class=\"fieldlabel\">\n                " . $aInt->lang("global", "sendemail") . "\n            </td>\n            <td class=\"fieldarea\">\n                <label class=\"checkbox-inline\">\n                    <input type=\"hidden\" name=\"sendconfirmation\" value=\"0\">\n                    <input type=\"checkbox\" name=\"sendconfirmation\" value=\"1\" " . $addPaymentSendConfirmationChecked . " >\n                    " . $aInt->lang("invoices", "ticksendconfirmation") . "\n                </label>\n            </td>\n        </tr>\n    </table>\n    <div class=\"btn-container\">\n        <button id=\"btnAddPayment\" type=\"submit\" class=\"btn btn-primary\">\n            <span id=\"paymentText\">\n                " . $aInt->lang("invoices", "addpayment") . "\n            </span>\n            <span id=\"paymentLoading\" class=\"hidden\">\n                <i class=\"fas fa-spinner fa-spin\"></i> " . $aInt->lang("global", "loading") . "\n            </span>\n        </button>\n    </div>\n    </form>";
    } else {
        $phpSelf = $whmcs->getPhpSelf();
        $token = generate_token("link");
        if($status == "Draft") {
            $publishText = $aInt->lang("invoices", "publish");
            $publishLink = "<a href=\"" . $phpSelf . "?action=edit&id=" . $id . "&tab=1\">\n    " . $publishText . "\n</a>";
            infoBox($aInt->lang("invoices", "invoiceIsDraft"), sprintf($aInt->lang("invoices", "invoiceIsCancelledDescription"), $publishLink));
        } else {
            $markUnpaid = $aInt->lang("invoices", "markunpaid");
            $markPaidLink = "<a href=\"" . $phpSelf . "?action=edit&id=" . $id . "&sub=statusunpaid&tab=1" . $token . "\">\n    " . $markUnpaid . "\n</a>";
            infoBox($aInt->lang("invoices", "invoiceIsCancelled"), sprintf($aInt->lang("invoices", "invoiceIsCancelledDescription"), $markPaidLink));
        }
        echo $infobox;
    }
    echo $aInt->nextAdminTab();
    echo "\n<form method=\"post\" action=\"";
    echo $whmcs->getPhpSelf();
    echo "\">\n<input type=\"hidden\" name=\"action\" value=\"edit\">\n<input type=\"hidden\" name=\"saveoptions\" value=\"true\">\n<input type=\"hidden\" name=\"id\" value=\"";
    echo $id;
    echo "\" id=\"invoiceId\">\n\n<table class=\"form\" width=\"100%\" border=\"0\" cellspacing=\"2\" cellpadding=\"3\">\n<tr>\n    <td width=\"20%\" class=\"fieldlabel\">\n        ";
    echo $aInt->lang("fields", "invoicedate");
    echo "    </td>\n    <td class=\"fieldarea\">\n        <div class=\"form-group date-picker-prepend-icon\">\n            <label for=\"inputInvoiceDate\" class=\"field-icon\">\n                <i class=\"fal fa-calendar-alt\"></i>\n            </label>\n            <input id=\"inputInvoiceDate\"\n                   type=\"text\"\n                   name=\"invoicedate\"\n                   value=\"";
    echo $date;
    echo "\"\n                   class=\"form-control date-picker-single\"\n            />\n        </div>\n    </td>\n    <td width=\"20%\" class=\"fieldlabel\">\n        ";
    echo $aInt->lang("fields", "duedate");
    echo "    </td>\n    <td class=\"fieldarea\">\n        <div class=\"form-group date-picker-prepend-icon\">\n            <label for=\"inputDateDue\" class=\"field-icon\">\n                <i class=\"fal fa-calendar-alt\"></i>\n            </label>\n            <input id=\"inputDateDue\"\n                   type=\"text\"\n                   name=\"datedue\"\n                   value=\"";
    echo $duedate;
    echo "\"\n                   class=\"form-control date-picker-single future\"\n            />\n        </div>\n    </td>\n</tr>\n<tr><td class=\"fieldlabel\">";
    echo $aInt->lang("fields", "paymentmethod");
    echo "</td><td class=\"fieldarea\">";
    echo paymentMethodsSelection();
    echo "</td><td class=\"fieldlabel\">";
    echo $aInt->lang("fields", "taxrate");
    echo "</td><td class=\"fieldarea\"><div class=\"form-inline\">\n    <div class=\"input-group input-group-140px\">\n        <div class=\"input-group-addon\">1</div>\n        <input type=\"text\" name=\"taxrate\" value=\"";
    echo $taxrate;
    echo "\" class=\"form-control input-md-80px\">\n        <div class=\"input-group-addon\">%</div>\n    </div>\n\n    <div class=\"input-group input-group-140px\">\n        <div class=\"input-group-addon\">2</div>\n        <input type=\"text\" name=\"taxrate2\" value=\"";
    echo $taxrate2;
    echo "\" class=\"form-control input-md-80px\">\n        <div class=\"input-group-addon\">%</div>\n    </div>\n</div></td></tr>\n<tr><td class=\"fieldlabel\">";
    echo $aInt->lang("fields", "invoicenum");
    echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"invoicenum\" value=\"";
    echo $invoicenum;
    echo "\" class=\"form-control input-150\"></td><td class=\"fieldlabel\">";
    echo $aInt->lang("fields", "status");
    echo "</td><td class=\"fieldarea\"><select name=\"status\" class=\"form-control select-inline\">\n";
    foreach (WHMCS\Invoices::getInvoiceStatusValues() as $invoiceStatusOption) {
        $isSelected = $status == $invoiceStatusOption;
        echo "<option value=\"" . $invoiceStatusOption . "\"" . ($isSelected ? " selected" : "") . ">" . $aInt->lang("status", strtolower(str_replace(" ", "", $invoiceStatusOption))) . "</option>";
    }
    echo "</select></td></tr>\n</table>\n<div class=\"btn-container\">\n    <input type=\"submit\" value=\"";
    echo $aInt->lang("global", "savechanges");
    echo "\" class=\"button btn btn-primary\">\n</div>\n</form>\n\n";
    echo $aInt->nextAdminTab();
    echo "\n    ";
    $totalCredit = get_query_val("tblclients", "credit", ["id" => $userid]);
    $currencyStep = $currency["format"] == 4 ? "1" : "0.01";
    echo "    <div class=\"row text-center\">\n        <div class=\"col-md-offset-2 col-md-4 col-sm-12\">\n            <b>";
    echo AdminLang::trans("invoices.addcredit");
    echo "</b>\n            <form method=\"post\" action=\"";
    echo fqdnRoutePath("admin-billing-invoice-add-credit", $id);
    echo "\">\n                <input type=\"number\" min=\"0\" step=\"";
    echo $currencyStep;
    echo "\" name=\"addcredit\"\n                       value=\"";
    echo $balance <= $totalCredit ? $balance : $totalCredit;
    echo "\"\n                       class=\"form-control input-100 input-inline\"";
    echo $totalCredit == "0.00" ? " disabled" : "";
    echo ">\n                <input type=\"submit\" value=\"";
    echo AdminLang::trans("global.go");
    echo "\"\n                       class=\"btn btn-default";
    echo $totalCredit == "0.00" ? " disabled" : "";
    echo "\"\n                    ";
    echo $totalCredit == "0.00" ? " disabled" : "";
    echo ">\n            </form>\n            <span style=\"color: #377D0D;\">\n                ";
    echo formatCurrency($totalCredit);
    echo "                ";
    echo AdminLang::trans("invoices.creditavailable");
    echo "            </span>\n        </div>\n        <div class=\"col-md-4 col-sm-12\">\n            <b>";
    echo AdminLang::trans("invoices.removecredit");
    echo "</b>\n            <form method=\"post\" action=\"";
    echo fqdnRoutePath("admin-billing-invoice-remove-credit", $id);
    echo "\">\n                <input type=\"number\" min=\"0\" step=\"";
    echo $currencyStep;
    echo "\" name=\"removecredit\" value=\"0.00\"\n                       class=\"form-control input-100 input-inline\"";
    echo $credit == "0.00" ? " disabled" : "";
    echo ">\n                <input type=\"submit\" value=\"";
    echo AdminLang::trans("global.go");
    echo "\"\n                       class=\"btn btn-default";
    echo $credit == "0.00" ? " disabled" : "";
    echo "\"\n                    ";
    echo $credit == "0.00" ? " disabled" : "";
    echo ">\n            </form>\n            <span style=\"color: #cc0000;\">\n                ";
    echo formatCurrency($credit);
    echo "                ";
    echo AdminLang::trans("invoices.creditavailable");
    echo "            </span>\n        </div>\n    </div>\n</form>\n\n";
    echo $aInt->nextAdminTab();
    echo "\n";
    $numtrans = get_query_vals("tblaccounts", "COUNT(id)", ["invoiceid" => $id, "amountin" => ["sqltype" => ">", "value" => "0"]], "date` ASC,`id", "ASC");
    $notransactions = $numtrans[0] == "0" ? true : false;
    $affiliatedHistoriesCount = WHMCS\Affiliate\History::where("invoice_id", $id)->count();
    $affiliatedPendingCount = WHMCS\Affiliate\Pending::where("invoice_id", $id)->count();
    if(0 < $affiliatedHistoriesCount + $affiliatedPendingCount) {
        $onSubmitString = "reverseCommissionConfirm(" . ($credit + $total) . ", " . $invoice->getData("balance") . ");return false;";
    }
    unset($affiliatedHistoriesCount);
    unset($affiliatedPendingCount);
    echo "<form method=\"post\" id=\"transactions\" action=\"";
    echo $whmcs->getPhpSelf();
    echo "\" onsubmit=\"";
    echo $onSubmitString ?? "";
    echo "\">\n<input type=\"hidden\" name=\"action\" value=\"edit\">\n<input type=\"hidden\" name=\"id\" value=\"";
    echo $id;
    echo "\">\n<input type=\"hidden\" name=\"sub\" value=\"refund\">\n\n<table class=\"form\" width=\"100%\" border=\"0\" cellspacing=\"2\" cellpadding=\"3\">\n<tr><td width=\"20%\" class=\"fieldlabel\">";
    echo $aInt->lang("invoices", "transactions");
    echo "</td><td class=\"fieldarea\"><select id=\"transid\" name=\"transid\" class=\"form-control select-inline\">";
    $result = select_query("tblaccounts", "", ["invoiceid" => $id, "amountin" => ["sqltype" => ">", "value" => "0"]], "date` ASC,`id", "ASC");
    $transArr = [];
    while ($data = mysql_fetch_array($result)) {
        $trans_id = $data["id"];
        $trans_date = $data["date"];
        $trans_amountin = $data["amountin"];
        $transArr[$trans_id] = $trans_amountin;
        $trans_transid = $data["transid"];
        $trans_date = fromMySQLDate($trans_date);
        $trans_amountin = formatCurrency($trans_amountin);
        echo "<option value=\"" . $trans_id . "\" data-amount=\"" . $data["amountin"] . "\">\n    " . $trans_date . " | " . $trans_transid . " | " . $trans_amountin . "\n</option>";
        $transInvoice = $data;
    }
    if($notransactions) {
        echo "<option value=\"\">" . $aInt->lang("invoices", "notransactions") . "</option>";
    }
    echo "</select></td></tr>\n<tr><td class=\"fieldlabel\">";
    echo $aInt->lang("fields", "amount");
    echo "</td><td class=\"fieldarea\"><div class=\"input-group input-300\"><input type=\"text\" name=\"amount\" id=\"amount\" class=\"form-control\" placeholder=\"0.00\"><span class=\"input-group-addon\">Leave blank for full refund</span></div></td></tr>\n<tr><td class=\"fieldlabel\">";
    echo $aInt->lang("invoices", "refundtype");
    echo "</td><td class=\"fieldarea\"><select name=\"refundtype\" id=\"refundtype\" class=\"form-control select-inline\" onchange=\"showrefundtransid();return false\"><option value=\"sendtogateway\">";
    echo $aInt->lang("invoices", "refundtypegateway");
    echo "</option><option value=\"\" type=\"\">";
    echo $aInt->lang("invoices", "refundtypemanual");
    echo "</option><option value=\"addascredit\">";
    echo $aInt->lang("invoices", "refundtypecredit");
    echo "</option></select></td></tr>\n<tr id=\"refundtransid\" style=\"display:none;\" ><td class=\"fieldlabel\">";
    echo $aInt->lang("fields", "transid");
    echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"refundtransid\" size=\"25\" class=\"form-control\" /></td></tr>\n<tr>\n    <td class=\"fieldlabel\">\n        ";
    echo AdminLang::trans("invoices.reverse");
    echo "    </td>\n    <td class=\"fieldarea\">\n        <label class=\"checkbox-inline\">\n            <input type=\"hidden\" name=\"reverse\" value=\"0\" />\n            <input type=\"checkbox\" name=\"reverse\" value=\"1\" /> ";
    echo AdminLang::trans("invoices.reverseDescription");
    echo "        </label>\n    </td>\n</tr>\n<tr>\n    <td class=\"fieldlabel\">\n        ";
    echo $aInt->lang("global", "sendemail");
    echo "    </td>\n    <td class=\"fieldarea\">\n        <label class=\"checkbox-inline\">\n            <input type=\"checkbox\" name=\"sendemail\" checked> ";
    echo $aInt->lang("invoices", "ticksendconfirmation");
    echo "        </label>\n    </td>\n</tr>\n";
    $creditGiven = false;
    if(isset($transInvoice["invoiceid"])) {
        $invoiceCredit = WHMCS\Database\Capsule::table("tblcredit")->where("relid", $transInvoice["invoiceid"])->sum("amount");
        if(0 < $invoiceCredit) {
            $creditGiven = true;
            echo "<tbody id='creditArea'>\n";
            $labelText = $aInt->lang("invoices", "invoiceCreditResult") . formatCurrency($invoiceCredit) . ". " . $aInt->lang("invoices", "currentCreditBalance") . formatCurrency($totalCredit) . ".";
            echo "<tr><td class=\"fieldlabel\"><font color=\"#cc0000\">WARNING</font></td><td class=\"fieldarea\">" . $labelText . "</td></tr>" . "\n";
            if($totalCredit < $invoiceCredit) {
                $labelText = $aInt->lang("invoices", "cannotRemoveCredit");
                $checkboxText = "<strong>" . $aInt->lang("invoices", "cannotRemoveCreditAck") . "</strong>";
            } else {
                $labelText = $aInt->lang("invoices", "creditCanBeRemoved");
                $radioButtons = ["removeCredit" => "<strong>" . $aInt->lang("invoices", "removeCreditFirst") . "</strong>", "leaveCredit" => "<strong>" . $aInt->lang("invoices", "leaveCreditUntouched") . "</strong>"];
            }
            echo "<tr><td class=\"fieldlabel\"></td><td class=\"fieldarea\">" . $labelText . "</td></tr>" . "\n";
            if(isset($checkboxText)) {
                echo "<tr><td class=\"fieldlabel\"></td>";
                echo "<td class=\"fieldarea\"><label class=\"checkbox-inline\"><input type=\"checkbox\" id=\"warning\" name=\"warning\" value=\"leaveCredit\" onclick=\"selectRefundChoice(this);\">" . $checkboxText . "</label></td>";
                echo "</tr>\n";
            } elseif(is_array($radioButtons)) {
                foreach ($radioButtons as $key => $button) {
                    echo "<tr><td class=\"fieldlabel\"></td>";
                    echo "<td class=\"fieldarea\"><label class=\"radio-inline\"><input type=\"radio\" id=\"warning_" . $key . "\" name=\"warning\" value=\"" . $key . "\" onclick=\"selectRefundChoice(this);\">" . $button . "</label></td>";
                    echo "</tr>\n";
                }
            }
            echo "<input type=\"hidden\" name=\"invoiceCredit\" id=\"invoiceCredit\" value=\"" . $invoiceCredit . "\">" . "\n";
            echo "</tbody>\n";
        }
    }
    if(!isset($invoiceCredit) || !is_numeric($invoiceCredit)) {
        $invoiceCredit = 0;
    }
    $transAmountObjectTxt = "";
    foreach ($transArr as $k => $v) {
        $transAmountObjectTxt .= "       transAmountObj._" . $k . " = " . $v . ";\n";
    }
    $aInt->jquerycode .= "\$(\"#transactions\").submit(function(e) {\n   var credit = " . $invoiceCredit . ";" . "\n" . "   var choice = \$(\"input[id^=warning]:checked\", \"#transactions\").val();" . "\n" . "   if (credit > 0 && choice != \"leaveCredit\") {" . "\n" . "       var amount = \$(\"#amount\").val();" . "\n" . "       amount = amount.replace(/^\\s*/, \"\").replace(/\\s*\$/, \"\");" . "\n" . "       " . "\n" . "       // Grab the amount from the combobox choice." . "\n" . "       var selectedId = \"_\" + \$(\"#transid\").find(\"option:selected\").val();" . "\n" . "       var transAmountObj = new Object();" . "\n" . $transAmountObjectTxt . "\n" . "       var transAmount = transAmountObj[selectedId];" . "\n" . "       " . "\n" . "       if (amount === \"\") {" . "\n" . "           // Field was left blank." . "\n" . "           // Return the entire amount." . "\n" . "           amount = transAmount;" . "\n" . "       }" . "\n" . "       " . "\n" . "       var removeCreditAmount;" . "\n" . "       if (amount < credit) {" . "\n" . "           // Only remove some of the credit." . "\n" . "           removeCreditAmount = amount;" . "\n" . "       } else if (amount >= credit) {" . "\n" . "           // Remove all credit." . "\n" . "           removeCreditAmount = credit;" . "\n" . "       } else {" . "\n" . "           // We do not have numbers." . "\n" . "           return;" . "\n" . "       }" . "\n" . "       " . "\n" . "       // Update the hidden credit field." . "\n" . "       \$(\"#invoiceCredit\").val(removeCreditAmount);" . "\n" . "   }" . "\n" . "});\n";
    $aInt->jquerycode .= "jQuery(\"#addPayment\").submit(function(event) {\n    // Only allow the first submission.\n    if (jQuery(this).data(\"alreadySent\") === true) {\n        event.preventDefault();\n    } else {\n        jQuery(this).data(\"alreadySent\", true);\n    }\n});";
    echo "</table>\n<div class=\"btn-container\">\n    <input type=\"submit\" value=\"";
    echo $aInt->lang("invoices", "refund");
    echo "\" class=\"btn btn-default\" id=\"refundBtn\"";
    if($notransactions || $creditGiven) {
        echo " disabled";
    }
    echo ">\n</div>\n</form>\n\n";
    echo $aInt->nextAdminTab();
    echo "\n<form method=\"post\" action=\"";
    echo $whmcs->getPhpSelf();
    echo "?save=notes\">\n<input type=\"hidden\" name=\"action\" value=\"edit\">\n<input type=\"hidden\" name=\"id\" value=\"";
    echo $id;
    echo "\">\n<textarea rows=4 style=\"width:100%\" name=\"notes\" class=\"form-control\">";
    echo $notes;
    echo "</textarea>\n<div class=\"btn-container\">\n    <input type=\"submit\" value=\"";
    echo $aInt->lang("global", "savechanges");
    echo "\" class=\"btn btn-primary\">\n</div>\n</form>\n\n";
    echo $aInt->endAdminTabs();
    echo "\n<script language=\"JavaScript\">\nfunction selectRefundChoice(selection)\n{\n    if (selection.checked) {\n        // A choice was made.\n        // Enable the refund button.\n        \$(\"#refundBtn\").removeAttr(\"disabled\");\n    } else {\n        // Checkbox was unchecked.\n        // Disable the refund button.\n        \$(\"#refundBtn\").prop(\"disabled\", \"disabled\");\n    }\n}\n</script>\n\n<h2 class=\"clearfix\">\n    ";
    echo $aInt->lang("invoices", "items");
    echo "</h2>\n<form id=\"frmInvoiceItems\" method=\"post\"\n      action=\"";
    echo $whmcs->getPhpSelf();
    echo "\"\n      onreset=\"handleInvoiceItemReset(event)\"\n>\n<input type=\"hidden\" name=\"action\" value=\"edit\">\n<input type=\"hidden\" name=\"id\" value=\"";
    echo $id;
    echo "\">\n<input type=\"hidden\" name=\"userid\" value=\"";
    echo $userid;
    echo "\">\n<input type=\"hidden\" name=\"sub\" value=\"save\">\n\n<div class=\"tablebg\">\n<table class=\"datatable\" width=\"100%\" border=\"0\" cellspacing=\"1\" cellpadding=\"3\">\n<tr>\n    <th width=\"20\"></th>\n    <th>";
    echo $aInt->lang("fields", "description");
    echo "</th>\n    <th width=\"120\">";
    echo $aInt->lang("fields", "amount");
    echo "</th>\n    <th width=\"70\">";
    echo $aInt->lang("fields", "taxed");
    echo "</th>\n    <th width=\"20\"></th>\n</tr>\n";
    $addTaxChecked = WHMCS\Config\Setting::getValue("TaxEnabled") && WHMCS\Config\Setting::getValue("TaxCustomInvoices");
    $invoiceItems = WHMCS\Billing\Invoice\Item::where("invoiceid", $id)->orderBy("id", "asc")->get();
    foreach ($invoiceItems as $index => $item) {
        echo "<tr data-index=\"";
        echo $index;
        echo "\">\n    <td width=\"20\" align=\"center\">\n        <input type=\"hidden\" name=\"items[";
        echo $index;
        echo "][itemid]\" value=\"";
        echo $item->id;
        echo "\" />\n        <input type=\"checkbox\" name=\"items[";
        echo $index;
        echo "][id]\" value=\"";
        echo $item->id;
        echo "\" />\n    </td>\n    <td>\n        <textarea name=\"items[";
        echo $index;
        echo "][description]\"\n                  rows=\"";
        echo count(explode("\n", $item->description));
        echo "\"\n                  class=\"form-control\"\n        >";
        echo $item["description"];
        echo "</textarea>\n    </td>\n    <td align=\"center\" nowrap>\n        <input type=\"text\"\n               name=\"items[";
        echo $index;
        echo "][amount]\"\n               value=\"";
        echo $item->amount;
        echo "\"\n               style=\"text-align:center\"\n               class=\"form-control\"\n               onblur=\"updateInvoiceTotal()\"\n        />\n    </td>\n    <td align=\"center\">\n        <input type=\"checkbox\"\n               name=\"items[";
        echo $index;
        echo "][taxed]\"\n               value=\"1\"\n               onchange=\"updateInvoiceTotal()\" ";
        echo $item->taxed ? " checked" : "";
        echo "        />\n    </td>\n    <td width=\"20\" align=\"center\">\n        <a href=\"#\" onClick=\"deleteInvoiceItem(event); return false\">\n            <img src=\"images/delete.gif\" border=\"0\">\n        </a>\n    </td>\n</tr>\n";
    }
    echo "\n<tr class=\"addCloneBefore\">\n    <td colspan=\"5\">\n        <button class=\"btn btn-default pull-right\" id=\"addNewItem\" onclick=\"addInvoiceItem(); return false;\">\n            ";
    echo AdminLang::trans("invoices.addItem");
    echo "        </button>\n    </td>\n</tr>\n\n<tr>\n    <td colspan=\"2\" style=\"text-align:right;background-color:#efefef;\">\n        <div align=\"left\" style=\"width:60%;float:left;\">\n            <select name=\"selaction\" onchange=\"handleBatchAction(this)\">\n                <option value=\"\">- ";
    echo $aInt->lang("global", "withselected");
    echo " -</option>\n                <option value=\"split\">";
    echo $aInt->lang("invoices", "split");
    echo "</option>\n                <option value=\"delete\">";
    echo $aInt->lang("global", "delete");
    echo "</option>\n            </select>\n        </div>\n        <div style=\"width:25%;float:right;line-height:22px;\">\n            <strong>";
    echo $aInt->lang("fields", "subtotal");
    echo ":</strong>&nbsp;\n        </div>\n    </td>\n    <td style=\"background-color:#efefef;text-align:center;\"><strong id=\"invoiceSubtotal\">";
    echo formatCurrency($subtotal);
    echo "</strong></td>\n    <td style=\"background-color:#efefef;\">&nbsp;</td>\n    <td style=\"background-color:#efefef;\"></td>\n</tr>\n";
    if($CONFIG["TaxEnabled"] == "on") {
        if($taxrate != "0.00") {
            echo "<tr><td colspan=\"2\" style=\"text-align:right;background-color:#efefef;\">";
            echo $taxrate;
            echo "% ";
            $taxdata = getTaxRate(1, $clientstate, $clientcountry);
            echo $taxdata["name"] ? $taxdata["name"] : $aInt->lang("invoices", "taxdue");
            echo ":&nbsp;</td><td style=\"background-color:#efefef;text-align:center;\" id=\"invoiceTax\">";
            echo formatCurrency($tax);
            echo "</td><td style=\"background-color:#efefef;\">&nbsp;</td><td style=\"background-color:#efefef;\">&nbsp;</td></tr>";
        }
        if($taxrate2 != "0.00") {
            echo "<tr><td colspan=\"2\" style=\"text-align:right;background-color:#efefef;\">";
            echo $taxrate2;
            echo "% ";
            $taxdata = getTaxRate(2, $clientstate, $clientcountry);
            echo $taxdata["name"] ? $taxdata["name"] : $aInt->lang("invoices", "taxdue");
            echo ":&nbsp;</td><td style=\"background-color:#efefef;text-align:center;\" id=\"invoiceTax2\">";
            echo formatCurrency($tax2);
            echo "</td><td style=\"background-color:#efefef;\">&nbsp;</td><td style=\"background-color:#efefef;\">&nbsp;</td></tr>";
        }
    }
    echo "<tr>\n    <td colspan=\"2\" style=\"text-align:right;background-color:#efefef;\">\n        ";
    echo $aInt->lang("fields", "credit");
    echo ":&nbsp;\n    </td>\n    <td style=\"background-color:#efefef;text-align:center;\" id=\"invoiceCredit\">\n        ";
    echo formatCurrency($credit);
    echo "    </td>\n    <td style=\"background-color:#efefef;\">&nbsp;</td>\n    <td style=\"background-color:#efefef;\">&nbsp;</td>\n</tr>\n<tr>\n    <th colspan=\"2\" style=\"text-align:right;\">\n        ";
    echo $aInt->lang("fields", "totaldue");
    echo ":&nbsp;\n    </th>\n    <th id=\"invoiceTotal\">";
    echo formatCurrency($total);
    echo "</th>\n    <th></th>\n    <th></th>\n</tr>\n</table>\n</div>\n<p align=center>\n    <input type=\"button\"\n       onclick=\"handleInvoiceSaveChanges(this)\"\n       value=\"";
    echo $aInt->lang("global", "savechanges");
    echo "\"\n       class=\"btn btn-primary\"\n    />\n    <input type=\"reset\" value=\"";
    echo $aInt->lang("global", "cancelchanges");
    echo "\" class=\"button btn btn-default\" />\n</p>\n</form>\n\n<script type=\"text/javascript\">\n    addInvoiceItem();\n\n    function handleInvoiceItemReset(event) {\n        clearValidation(event.target);\n\n        setTimeout(function() {\n            updateInvoiceTotal(true);\n        }, 0);\n    }\n\n    function handleInvoiceItemInput(event) {\n        if (event.target.name === 'selaction') {\n            return;\n        }\n\n        const form = jQuery(event.target).closest('form');\n        const initialFormState = form.data('initialState');\n        const currentFormState = serializeForm(form);\n\n        if (currentFormState === initialFormState) {\n            jQuery('#unsavedChangesAlert').addClass('hidden');\n        } else {\n            jQuery('#unsavedChangesAlert').removeClass('hidden');\n        }\n    }\n\n    function serializeForm(formQuery) {\n        if (formQuery.attr('id') !== 'frmInvoiceItems') {\n            return formQuery.serialize();\n        }\n\n        const filteredRows = formQuery.find('tr').filter(function() {\n            const description = jQuery(this).find('textarea[name*=\"[description]\"]').val();\n            const amount = jQuery(this).find('input[name*=\"[amount]\"]').val();\n\n            return description || amount;\n        });\n\n        const serializedArray = filteredRows.find(':input')\n            .serializeArray()\n            .filter(function(item) {\n                return !(/^items\\[\\d+\\]\\[id\\]\$/.test(item.name));\n            });\n\n        return jQuery.param(serializedArray);\n    }\n\n    function deleteInvoiceItem(event) {\n        const trQuery = jQuery(event.currentTarget).closest('tr');\n        const formQuery = trQuery.closest('form');\n        swal(\n            {\n                title: \"";
    echo addslashes(AdminLang::trans("global.areYouSure"));
    echo "\",\n                text: \"";
    echo addslashes(AdminLang::trans("invoices.deletelineitem"));
    echo "\",\n                type: 'warning',\n                showCancelButton: true,\n                confirmButtonColor: \"#DD6B55\",\n                closeOnConfirm: false,\n                showLoaderOnConfirm: true\n            },\n            function() {\n                const value = trQuery.find('input[name\$=\"[id]\"]').val();\n                const id = parseInt(value, 10);\n                if (jQuery.isNumeric(id)) {\n                    destroyInvoiceItem(id).done(function(data) {\n                        if (!data.success) {\n                            jQuery.growl.error({ title: \"Error\", message: data.errorMessage });\n                            return;\n                        }\n                        trQuery.next('tr.has-error').remove();\n                        trQuery.remove();\n                        updateInvoiceTotal(true);\n                        resetFormState(formQuery);\n                        swal(\n                            \"";
    echo addslashes(AdminLang::trans("global.success"));
    echo "\",\n                            \"";
    echo addslashes(AdminLang::trans("global.changesuccessdeleted"));
    echo "\",\n                            \"success\"\n                        );\n                    })\n                } else {\n                    trQuery.next('tr.has-error').remove();\n                    trQuery.remove();\n                    updateInvoiceTotal(true);\n                    updateFormDirty(formQuery);\n                    setTimeout(function() {\n                        swal(\n                            \"";
    echo addslashes(AdminLang::trans("global.success"));
    echo "\",\n                            \"";
    echo addslashes(AdminLang::trans("global.changesuccessdeleted"));
    echo "\",\n                            \"success\"\n                        );\n                    }, 0);\n                }\n            }\n        )\n    }\n\n    function addInvoiceItem() {\n        const lastItem = jQuery('#frmInvoiceItems tr:has(input)').last();\n        const index = lastItem.length === 0 ? 0 : parseInt(lastItem.attr('data-index')) + 1;\n\n        const invoiceItem = jQuery(renderInvoiceItem(index));\n        invoiceItem.insertBefore('tr.addCloneBefore');\n\n        invoiceItem.find('textarea[name^=\"items\"][name\$=\"[description]\"]').focus();\n    }\n\n    let updateInvoiceTotalTimeout;\n    function updateInvoiceTotal(isForce) {\n        if (updateInvoiceTotalTimeout) {\n            clearTimeout(updateInvoiceTotalTimeout);\n        }\n\n        updateInvoiceTotalTimeout = setTimeout(function() {\n            const items = [];\n            jQuery('#frmInvoiceItems tr:has(input)').each(function() {\n                const description = jQuery(this).find('textarea[name\$=\"[description]\"]').val();\n                const amount = jQuery(this).find('input[name\$=\"[amount]\"]').val();\n                const taxed = jQuery(this).find('input[name\$=\"[taxed]\"]').is(':checked') ? 1 : 0;\n\n                if (!description || !amount) {\n                    return;\n                }\n\n                items.push({\n                    description: description,\n                    amount: amount,\n                    taxed: taxed\n                });\n            });\n\n            if (items.length === 0 && !isForce) {\n                return;\n            }\n\n            jQuery('#invoiceTotal').html('Calculating...');\n            jQuery('#invoiceSubtotal').html('Calculating...');\n            jQuery('#invoiceCredit').html('Calculating...');\n            jQuery('#invoiceTax').html('Calculating...');\n            jQuery('#invoiceTax2').html('Calculating...');\n\n            calculateInvoiceTotal(";
    echo $id;
    echo ", items)\n                .done(function(data) {\n                    const invoiceTotal = data.invoiceTotal;\n                    if (data.invoiceTotal === null) {\n                        return;\n                    }\n\n                    jQuery('#invoiceTotal').html(invoiceTotal.total);\n                    jQuery('#invoiceSubtotal').html(invoiceTotal.subtotal);\n                    jQuery('#invoiceCredit').html(invoiceTotal.credit);\n                    jQuery('#invoiceTax').html(invoiceTotal.tax);\n                    jQuery('#invoiceTax2').html(invoiceTotal.tax2);\n                    jQuery('#summaryInvoiceAmount').html(invoiceTotal.invoiceAmount);\n                    jQuery('#summaryBalance').html(invoiceTotal.balance);\n                });\n        }, 1000);\n    }\n\n    function renderInvoiceItem(index) {\n        return `\n            <tr data-index=\"\${index}\">\n                <td width=\"20\"></td>\n                <td>\n                    <textarea name=\"items[\${index}][description]\" rows=\"1\" class=\"form-control\"></textarea>\n                </td>\n                <td align=\"center\">\n                    <input type=\"text\"\n                           name=\"items[\${index}][amount]\"\n                           style=\"text-align:center\"\n                           class=\"form-control\"\n                           onblur=\"updateInvoiceTotal()\"\n                    >\n                </td>\n                <td align=\"center\">\n                    <input type=\"checkbox\"\n                           name=\"items[\${index}][taxed]\"\n                           value=\"1\"";
    echo $addTaxChecked ? " checked=\"checked\"" : "";
    echo "                           onchange=\"updateInvoiceTotal()\"\n                    >\n                </td>\n                <td>\n                    \${renderInvoiceItemDeleteButton()}\n                </td>\n            </tr>\n        `;\n    }\n\n    function renderInvoiceItemDeleteButton() {\n        return `\n            <a href=\"#\" onClick=\"deleteInvoiceItem(event); return false\">\n                <img src=\"images/delete.gif\" border=\"0\">\n            </a>\n        `;\n    }\n\n    function handleBatchAction(select) {\n        if (select.value === '') {\n            return;\n        }\n\n        jQuery(select.form).submit();\n    }\n\n    function handleInvoiceSaveChanges(button) {\n        let isValid = true;\n        const form = button.closest('form');\n\n        jQuery(form).find('tr.has-error').remove();\n        jQuery(form).find('td').removeClass('has-error');\n\n        jQuery(form).find('tr[data-index]').each(function() {\n            const description = jQuery(this).find('textarea[name^=\"items\"][name\$=\"[description]\"]').val();\n            const amount = jQuery(this).find('input[name^=\"items\"][name\$=\"[amount]\"]').val();\n\n            if ((description && !amount) || (!description && amount)) {\n                isValid = false;\n                jQuery(this).find('td').addClass('has-error');\n                const message = \"";
    echo addslashes(AdminLang::trans("invoices.itemDescriptionAmountRequired"));
    echo "\"\n                jQuery(this).after(renderValidationMessage(message));\n            }\n        });\n\n        if (!isValid) {\n            return;\n        }\n\n        jQuery(form).submit();\n    }\n\n    function clearValidation(form) {\n        jQuery(form).find('tr.has-error').remove();\n        jQuery(form).find('td').removeClass('has-error');\n    }\n\n    function renderValidationMessage(message) {\n        return `\n        <tr class=\"has-error\">\n            <td></td>\n            <td colspan=\"4\" class=\"control-label\">\${message}</td>\n        </tr>\n    `;\n    }\n\n    const formStateMap = {};\n    function updateFormDirty(formQuery) {\n        const formState = formStateMap[formQuery.data('formId')];\n\n        if (!formState) {\n            return;\n        }\n\n        formState.isDirty = formState.initialState !== serializeForm(formQuery);\n\n        if (formQuery.attr('id') !== 'frmInvoiceItems') {\n            return;\n        }\n        if (formState.isDirty) {\n            jQuery('#unsavedChangesAlert').removeClass('hidden');\n        } else {\n            jQuery('#unsavedChangesAlert').addClass('hidden');\n        }\n    }\n\n    function resetFormState(formQuery) {\n        const formState = formStateMap[formQuery.data('formId')];\n\n        if (!formState) {\n            return;\n        }\n\n        formState.initialState = serializeForm(formQuery);\n        formState.isDirty = false;\n    }\n\n    jQuery(document).ready(function() {\n        const action = getQueryParam('action');\n        if (action !== 'edit') {\n            return;\n        }\n\n        const contentAreaQuery = jQuery('#contentarea');\n        const formsQuery = contentAreaQuery.find('form');\n        formsQuery.each(function(index) {\n            const formQuery = jQuery(this);\n            const formId = 'form-' + index + '-' + Date.now();\n            formQuery.data('formId', formId);\n            formStateMap[formId] = {\n                ref: formQuery,\n                initialState: serializeForm(formQuery),\n                isDirty: false,\n            }\n\n            formQuery.on('change', function() {\n                updateFormDirty(formQuery);\n            });\n\n            formQuery.on('reset', function() {\n                setTimeout(function() {\n                    updateFormDirty(formQuery);\n                }, 0);\n            });\n\n            if (formQuery.attr('id') === 'addPayment') {\n                return;\n            }\n            formQuery.on('submit', function(event) {\n                handleUnsavedChanges(event, function() {\n                    formQuery.off('submit').submit();\n                });\n            });\n        });\n\n        const linksQuery = contentAreaQuery.find('a[href]:not([href^=\"#\"]):not(.open-modal)');\n        linksQuery.on('click', function(event) {\n            const href = jQuery(this).attr('href');\n            handleUnsavedChanges(event, function() {\n                window.location.href = href;\n            });\n        });\n\n        const buttonLinksQuery = contentAreaQuery.find('button[data-url]');\n        buttonLinksQuery.on('click', function(event) {\n            event.preventDefault();\n            handleUnsavedChanges(event, function() {\n                window.location.href = jQuery(event.currentTarget).data('url');\n            });\n        });\n    });\n\n    function handleUnsavedChanges(event, proceedCallback) {\n        let formIds = Object.keys(formStateMap);\n\n        if (event.target.tagName === 'FORM') {\n            const formQuery = jQuery(event.target);\n            const currentFormId = formQuery.data('formId');\n            const selactionElement = formQuery.find('select[name=\"selaction\"]');\n\n            if (!selactionElement.length || selactionElement.val() === '') {\n                formIds = formIds.filter(function(id) {\n                    return id !== currentFormId;\n                });\n            }\n        }\n\n        const anyUnsavedChanges = formIds.some(function(id) {\n            return formStateMap[id].isDirty;\n        });\n\n        if (anyUnsavedChanges) {\n            event.preventDefault();\n            swal(\n                {\n                    title: \"";
    echo addslashes(AdminLang::trans("global.areYouSure"));
    echo "\",\n                    text: \"";
    echo addslashes(AdminLang::trans("invoices.unsavedChangesLost"));
    echo "\",\n                    type: 'warning',\n                    showCancelButton: true,\n                    confirmButtonColor: \"#DD6B55\",\n                    closeOnConfirm: false,\n                    showLoaderOnConfirm: true\n                },\n                function(isConfirm) {\n                    if (isConfirm) {\n                        proceedCallback();\n                    } else {\n                        const element = jQuery(event.target);\n                        if (element.attr('id') === 'frmInvoiceItems') {\n                            element.find('select[name=\"selaction\"]').val('');\n                        }\n                    }\n                }\n            );\n        } else {\n            proceedCallback();\n        }\n    }\n\n</script>\n\n<h2>";
    echo $aInt->lang("invoices", "transactions");
    echo "</h2>\n\n";
    $aInt->sortableTableInit("nopagination");
    $paymentGateways = new WHMCS\Gateways();
    $transactions = [];
    $paymentTransactions = WHMCS\Billing\Payment\Transaction::where("invoiceid", "=", (int) $id)->orderBy("date")->orderBy("id")->get();
    foreach ($paymentTransactions as $transaction) {
        $paymentmethod = "";
        if($transaction->paymentGateway) {
            $paymentmethod = $paymentGateways->getDisplayName($transaction->paymentGateway);
        }
        if(!$paymentmethod) {
            $paymentmethod = "-";
        }
        $transactions[(string) $transaction->date][] = [fromMySQLDate($transaction->date, 1), $paymentmethod, $transaction->getTransactionIdMarkup(), formatCurrency($transaction->amountin - $transaction->amountout), formatCurrency($transaction->fees), "<a href=\"#\" onClick=\"doDeleteTransaction('" . $transaction->id . "');return false\"><img src=\"images/delete.gif\" width=\"16\" height=\"16\" border=\"0\" alt=\"Delete\"></a>"];
    }
    $creditTransactions = WHMCS\Database\Capsule::table("tblcredit")->where("description", "LIKE", "%Invoice #" . (int) $id)->get()->all();
    foreach ($creditTransactions as $transaction) {
        if(0 < $transaction->amount) {
            if(strpos($transaction->description, "Overpayment") !== false || strpos($transaction->description, "Mass Invoice Payment Credit") !== false) {
            } else {
                $creditMsg = AdminLang::trans("invoices.creditRemoved");
            }
        } else {
            $creditMsg = AdminLang::trans("invoices.creditApplied");
        }
        $transactions[$transaction->date . " 25:59:59"][] = [fromMySQLDate($transaction->date), $creditMsg, "-", formatCurrency($transaction->amount * -1), "-", ""];
    }
    ksort($transactions);
    foreach ($transactions as $date => $trans) {
        foreach ($trans as $transaction) {
            $tabledata[] = $transaction;
        }
    }
    echo $aInt->sortableTable([$aInt->lang("fields", "date"), $aInt->lang("fields", "paymentmethod"), $aInt->lang("fields", "transid"), $aInt->lang("fields", "amount"), $aInt->lang("fields", "fees"), ""], $tabledata);
    $log = WHMCS\Billing\Payment\Transaction\History::where("invoice_id", $id)->get();
    echo "<h2>" . AdminLang::trans("invoices.transactionsHistory") . "</h2>";
    $tableData = [];
    foreach ($log as $transactionHistory) {
        $transHistTooltip = AdminLang::trans("invoices.transactionsHistoryTooltip");
        $transHistTransIdLink = "<a href=\"gatewaylog.php?history=" . $transactionHistory->id . "\">\n" . $transactionHistory->transactionId . "\n<i data-toggle=\"tooltip\"\n   data-container=\"body\"\n   data-placement=\"right auto\"\n   data-trigger=\"hover\"\n   class=\"fal fa-line-columns\"\n   title=\"" . $transHistTooltip . "\"\n></i>\n</a>";
        $tableData[] = [$transactionHistory->updatedAt->toAdminDateTimeFormat(), $paymentGateways->getDisplayName($transactionHistory->gateway), $transHistTransIdLink, $transactionHistory->remoteStatus, $transactionHistory->description];
    }
    echo $aInt->sortableTable([AdminLang::trans("fields.date"), AdminLang::trans("fields.paymentmethod"), AdminLang::trans("fields.transid"), AdminLang::trans("fields.status"), AdminLang::trans("fields.description")], $tableData);
    $affiliateHistories = WHMCS\Affiliate\History::with("affiliate", "affiliate.client")->where("invoice_id", $id);
    $affiliatePendings = WHMCS\Affiliate\Pending::with("account", "account.affiliate", "account.affiliate.client")->where("invoice_id", $id);
    if($affiliateHistories->count() || $affiliatePendings->count()) {
        echo "<h2>" . AdminLang::trans("affiliates.commissionshistory") . "</h2>";
        $tableData = [];
        foreach ($affiliatePendings->get() as $affiliatePending) {
            $affiliate = $affiliatePending->account->affiliate;
            $tableData[] = [$affiliatePending->createdAt->toAdminDateFormat(), "<a href=\"" . $affiliate->getFullAdminUrl() . "\" class=\"autoLinked\">" . $affiliate->client->fullName . "</a>", formatCurrency($affiliatePending->amount, $affiliate->client->currencyrel), AdminLang::trans("affiliates.pendingCommissionWillClear", [":clearDate" => $affiliatePending->clearingDate->toAdminDateFormat()])];
        }
        foreach ($affiliateHistories->get() as $affiliateHistory) {
            $affiliate = $affiliateHistory->affiliate;
            $tableData[] = [$affiliateHistory->date->toAdminDateFormat(), "<a href=\"" . $affiliate->getFullAdminUrl() . "\" class=\"autoLinked\">" . $affiliate->client->fullName . "</a>", formatCurrency($affiliateHistory->amount, $affiliate->client->currencyrel), $affiliateHistory->description];
        }
        echo $aInt->sortableTable([AdminLang::trans("fields.date"), AdminLang::trans("fields.affiliate"), AdminLang::trans("fields.amount"), AdminLang::trans("fields.description")], $tableData);
    }
    echo $aInt->modal("ReverseAffiliateCommission", AdminLang::trans("affiliates.reverseCommissionTitle"), AdminLang::trans("affiliates.reverseCommissionBody"), [["title" => AdminLang::trans("affiliates.reverseCommissionButton"), "onclick" => "reverseCommissionSubmit(true)"], ["title" => AdminLang::trans("global.no"), "class" => "btn-danger", "onclick" => "reverseCommissionSubmit()"]]);
    $jscode .= "\nvar thisInvoiceId = " . $invoiceModel->id . ";\n";
}
if(!empty($duplicateTransactionModal)) {
    echo $duplicateTransactionModal;
}
$content = ob_get_contents();
ob_end_clean();
$aInt->content = $content;
$aInt->jscode = $jscode;
$aInt->display();

?>