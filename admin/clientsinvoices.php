<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
define("ADMINAREA", true);
require "../init.php";
$aInt = new WHMCS\Admin("List Invoices", false);
$aInt->requiredFiles(["gatewayfunctions", "invoicefunctions", "processinvoices"]);
$aInt->setClientsProfilePresets();
$aInt->setHelpLink("Clients:Invoices Tab");
if(isset($delete) && $delete || isset($massdelete) && $massdelete) {
    checkPermission("Delete Invoice");
}
if(isset($markpaid) && $markpaid || isset($markunpaid) && $markunpaid) {
    checkPermission("Manage Invoice");
}
if(!empty($markcancelled)) {
    checkPermission("Cancel Invoice");
}
$userId = $aInt->valUserID($whmcs->get_req_var("userid"));
$aInt->assertClientBoundary($userid);
$page = (int) App::getFromRequest("page");
$reloadRedirect = function ($query) use($userId, $page) {
    $query["userid"] = $userId;
    if(!empty($page)) {
        $query["page"] = $page;
    }
    $query["filter"] = 1;
    redir($query);
};
if(isset($markpaid) && $markpaid) {
    check_token("WHMCS.admin.default");
    $failedInvoices = [];
    $invoiceCount = 0;
    foreach ($selectedinvoices as $invid) {
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
    $reloadRedirect();
}
if(isset($markunpaid) && $markunpaid) {
    check_token("WHMCS.admin.default");
    foreach ($selectedinvoices as $invid) {
        WHMCS\Database\Capsule::table("tblinvoices")->where("id", $invid)->update(["status" => WHMCS\Billing\Invoice::STATUS_UNPAID, "datepaid" => "0000-00-00 00:00:00", "date_cancelled" => "0000-00-00 00:00:00", "date_refunded" => "0000-00-00 00:00:00", "updated_at" => WHMCS\Carbon::now()->toDateTimeString()]);
        logActivity("Reactivated Invoice - Invoice ID: " . $invid, $userid);
        run_hook("InvoiceUnpaid", ["invoiceid" => $invid]);
    }
    $reloadRedirect();
}
if(isset($markcancelled) && $markcancelled) {
    check_token("WHMCS.admin.default");
    foreach ($selectedinvoices as $invid) {
        WHMCS\Database\Capsule::table("tblinvoices")->where("id", $invid)->update(["status" => WHMCS\Billing\Invoice::STATUS_CANCELLED, "date_cancelled" => WHMCS\Carbon::now()->toDateTimeString(), "updated_at" => WHMCS\Carbon::now()->toDateTimeString()]);
        logActivity("Cancelled Invoice - Invoice ID: " . $invid, $userid);
        run_hook("InvoiceCancelled", ["invoiceid" => $invid]);
    }
    $reloadRedirect();
}
if(!empty($duplicateinvoice)) {
    check_token("WHMCS.admin.default");
    checkPermission("Create Invoice");
    foreach (App::getFromRequest("selectedinvoices") as $invoiceId) {
        $invoices = new WHMCS\Invoices();
        $invoices->duplicate($invoiceId);
    }
    $reloadRedirect();
}
if(isset($massdelete) && $massdelete) {
    check_token("WHMCS.admin.default");
    foreach ($selectedinvoices as $invoiceId) {
        $invoice = WHMCS\User\Client::find($userId)->invoices->find($invoiceId);
        if($invoice) {
            $invoice->delete();
            logActivity("Deleted Invoice - Invoice ID: " . $invoiceId, $userId);
        }
    }
    $reloadRedirect();
}
if(isset($paymentreminder) && $paymentreminder) {
    check_token("WHMCS.admin.default");
    foreach ($selectedinvoices as $invid) {
        sendMessage("Invoice Payment Reminder", $invid);
        logActivity("Invoice Payment Reminder Sent - Invoice ID: " . $invid, $userid);
    }
    $reloadRedirect();
}
if(isset($merge) && $merge) {
    check_token("WHMCS.admin.default");
    checkPermission("Manage Invoice");
    if(count($selectedinvoices) < 2) {
        $reloadRedirect(["mergeerr" => 1]);
    }
    $selectedinvoices = db_escape_numarray($selectedinvoices);
    sort($selectedinvoices);
    $endinvoiceid = end($selectedinvoices);
    update_query("tblinvoiceitems", ["invoiceid" => $endinvoiceid], "invoiceid IN (" . db_build_in_array($selectedinvoices) . ")");
    update_query("tblaccounts", ["invoiceid" => $endinvoiceid], "invoiceid IN (" . db_build_in_array($selectedinvoices) . ")");
    update_query("tblorders", ["invoiceid" => $endinvoiceid], "invoiceid IN (" . db_build_in_array($selectedinvoices) . ")");
    foreach ($selectedinvoices as $replaceInvoiceId) {
        if($replaceInvoiceId !== $endinvoiceid) {
            WHMCS\Database\Capsule::connection()->update("UPDATE tblcredit SET description=CONCAT(description, \". Merged to Invoice #" . (int) $endinvoiceid . "\") WHERE description LIKE \"%Invoice #" . (int) $replaceInvoiceId . "\"");
        }
    }
    $result = select_query("tblinvoices", "SUM(credit)", "id IN (" . db_build_in_array($selectedinvoices) . ")");
    $data = mysql_fetch_array($result);
    $totalcredit = $data[0];
    $endInvoice = WHMCS\Billing\Invoice::find($endinvoiceid);
    $endInvoice->credit = $totalcredit;
    unset($selectedinvoices[count($selectedinvoices) - 1]);
    delete_query("tblinvoices", "id IN (" . db_build_in_array($selectedinvoices) . ")");
    $endInvoice->save();
    $endInvoice->updateInvoiceTotal();
    logActivity("Merged Invoice IDs " . db_build_in_array($selectedinvoices) . " to Invoice ID: " . $endinvoiceid, $userid);
    $reloadRedirect();
}
if(isset($masspay) && $masspay) {
    check_token("WHMCS.admin.default");
    if(count($selectedinvoices) < 2) {
        $reloadRedirect(["masspayerr" => 1]);
    }
    $invoiceid = createInvoices($userid);
    $paymentmethod = getClientsPaymentMethod($userid);
    $invoiceitems = [];
    foreach ($selectedinvoices as $invoiceid) {
        $result = select_query("tblinvoices", "", ["id" => $invoiceid]);
        $data = mysql_fetch_array($result);
        $subtotal += $data["subtotal"];
        $credit += $data["credit"];
        $tax += $data["tax"];
        $tax2 += $data["tax2"];
        $thistotal = $data["total"];
        $result = select_query("tblaccounts", "SUM(amountin)", ["invoiceid" => $invoiceid]);
        $data = mysql_fetch_array($result);
        $thispayments = $data[0];
        $thistotal = $thistotal - $thispayments;
        insert_query("tblinvoiceitems", ["userid" => $userid, "type" => "Invoice", "relid" => $invoiceid, "description" => $_LANG["invoicenumber"] . $invoiceid, "amount" => $thistotal, "duedate" => "now()", "paymentmethod" => $paymentmethod]);
    }
    $invoiceid = createInvoices($userid, true, true, ["invoices" => $selectedinvoices]);
    $reloadRedirect(["masspayid" => $invoiceid]);
}
if(isset($delete) && $delete) {
    check_token("WHMCS.admin.default");
    checkPermission("Delete Invoice");
    $invoiceID = (int) $whmcs->get_req_var("invoiceid");
    $invoice = WHMCS\User\Client::find($userId)->invoices->find($invoiceID);
    if($invoice) {
        if($whmcs->get_req_var("returnCredit")) {
            removeCreditOnInvoiceDelete($invoice);
        }
        $invoice->delete();
        logActivity("Deleted Invoice - Invoice ID: " . $invoiceID, $userId);
    }
    $reloadRedirect();
}
ob_start();
$currency = getCurrency($userid);
$jquerycode .= "jQuery(\".invtooltip\").invoiceTooltip({cssClass:\"invoicetooltip\"});";
$jsCode = "";
if(isset($mergeerr) && $mergeerr) {
    infoBox($aInt->lang("invoices", "mergeerror"), $aInt->lang("invoices", "mergeerrordesc"));
}
if(isset($masspayerr) && $masspayerr) {
    infoBox($aInt->lang("invoices", "masspay"), $aInt->lang("invoices", "mergeerrordesc"));
}
if(isset($masspayid) && $masspayid) {
    infoBox($aInt->lang("invoices", "masspay"), $aInt->lang("invoices", "masspaysuccess") . " - <a href=\"invoices.php?action=edit&id=" . (int) $masspayid . "\">" . $aInt->lang("fields", "invoicenum") . $masspayid . "</a>");
}
echo $infobox;
$filt = new WHMCS\Filter("clinv");
$filterops = ["serviceid", "addonid", "domainid", "clientname", "invoicenum", "lineitem", "paymentmethod", "invoicedate", "duedate", "datepaid", "totalfromtotalto", "status"];
$filt->setAllowedVars($filterops);
$invoices = WHMCS\Billing\Invoice::with(["client", "items", "transactions"]);
$invoices->where("userid", $userId);
if($serviceid = $filt->get("serviceid")) {
    $invoices->whereHas("items", function (Illuminate\Database\Eloquent\Builder $query) use($serviceid) {
        $query->where("type", WHMCS\Billing\InvoiceItemInterface::TYPE_SERVICE)->where("relid", $serviceid);
    });
}
if($addonid = $filt->get("addonid")) {
    $invoices->whereHas("items", function (Illuminate\Database\Eloquent\Builder $query) use($addonid) {
        $query->where("type", WHMCS\Billing\InvoiceItemInterface::TYPE_SERVICE_ADDON)->where("relid", $addonid);
    });
}
if($domainid = $filt->get("domainid")) {
    $invoices->whereHas("items", function (Illuminate\Database\Eloquent\Builder $query) use($domainid) {
        $query->whereIn("type", [WHMCS\Billing\InvoiceItemInterface::TYPE_DOMAIN, WHMCS\Billing\InvoiceItemInterface::TYPE_DOMAIN_REGISTRATION, WHMCS\Billing\InvoiceItemInterface::TYPE_DOMAIN_TRANSFER])->where("relid", $domainid);
    });
}
if($clientname = $filt->get("clientname")) {
    $invoices->whereHas("client", function (Illuminate\Database\Eloquent\Builder $query) use($clientname) {
        $query->whereRaw("concat(firstname,' ',lastname) LIKE '%" . $clientname . "%'");
    });
}
if($invoicenum = $filt->get("invoicenum")) {
    $invoices->where(function (Illuminate\Database\Eloquent\Builder $query) use($invoicenum) {
        $query->where("tblinvoices.id", $invoicenum)->orWhere("invoicenum", $invoicenum);
    });
}
if($lineitem = $filt->get("lineitem")) {
    $invoices->whereHas("items", function (Illuminate\Database\Eloquent\Builder $query) use($userId, $lineitem) {
        $query->where("userid", $userId)->where("description", "like", "%" . $lineitem . "%");
    });
}
if($paymentmethod = $filt->get("paymentmethod")) {
    $invoices->where("paymentmethod", $paymentmethod);
}
$dateFilters = ["invoicedate" => "date", "duedate" => "duedate", "datepaid" => "datepaid", "last_capture_attempt" => "last_capture_attempt", "date_refunded" => "date_refunded", "date_cancelled" => "date_cancelled"];
foreach ($dateFilters as $filterCriteria => $fieldName) {
    if(${$filterCriteria} = $filt->get($filterCriteria)) {
        $dateRange = WHMCS\Carbon::parseDateRangeValue(${$filterCriteria});
        $dateFrom = $dateRange["from"];
        $dateTo = $dateRange["to"];
        $invoices->whereBetween("tblinvoices." . $fieldName, [$dateFrom->toDateTimeString(), $dateTo->toDateTimeString()]);
    }
}
$totalFrom = $filt->get("totalfrom");
$totalTo = $filt->get("totalto");
if($totalFrom && $totalTo) {
    $invoices->whereBetween("total", [$totalFrom, $totalTo]);
} elseif($totalFrom) {
    $invoices->where("total", ">=", $totalFrom);
} elseif($totalTo) {
    $invoices->where("total", "<=", $totalTo);
}
if($status = $filt->get("status")) {
    if($status == "Overdue") {
        $invoices->overdue();
    } else {
        $invoices->where("tblinvoices.status", $status);
    }
}
$filt->store();
WHMCS\Session::release();
$failedInvoices = WHMCS\Input\Sanitize::makeSafeForOutput(WHMCS\Cookie::get("FailedMarkPaidInvoices", true));
$successfulInvoicesCount = 0;
if(isset($failedInvoices["successfulInvoicesCount"])) {
    $successfulInvoicesCount = (int) $failedInvoices["successfulInvoicesCount"];
    unset($failedInvoices["successfulInvoicesCount"]);
}
parent::delete("FailedMarkPaidInvoices");
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
echo "\n<form action=\"";
echo $whmcs->getPhpSelf();
echo "?userid=";
echo $userid;
echo "\" method=\"post\">\n\n<div class=\"context-btn-container\">\n    <button id=\"invoiceSearch\" type=\"submit\" class=\"btn btn-default\">\n        <i class=\"fas fa-search\"></i>\n        ";
echo $aInt->lang("global", "search");
echo "    </button>\n    <button type=\"button\" class=\"btn btn-primary\" onClick=\"window.location='invoices.php?action=createinvoice&userid=";
echo $userid . generate_token("link");
echo "'\" class=\"btn-success\">\n        <i class=\"fas fa-plus\"></i>\n        ";
echo $aInt->lang("invoices", "create");
echo "    </button>\n</div>\n\n<table class=\"form\" width=\"100%\" border=\"0\" cellspacing=\"2\" cellpadding=\"3\">\n    <tr>\n        <td width=\"15%\" class=\"fieldlabel\">\n            ";
echo AdminLang::trans("fields.invoicenum");
echo "        </td>\n        <td class=\"fieldarea\">\n            <input type=\"text\" name=\"invoicenum\" class=\"form-control input-150\" value=\"";
echo $invoicenum;
echo "\">\n        </td>\n        <td width=\"15%\" class=\"fieldlabel\">\n            ";
echo AdminLang::trans("fields.invoicedate");
echo "        </td>\n        <td class=\"fieldarea\">\n            <div class=\"form-group date-picker-prepend-icon\">\n                <label for=\"inputInvoiceDate\" class=\"field-icon\">\n                    <i class=\"fal fa-calendar-alt\"></i>\n                </label>\n                <input id=\"inputInvoiceDate\"\n                       type=\"text\"\n                       name=\"invoicedate\"\n                       value=\"";
echo $invoicedate;
echo "\"\n                       class=\"form-control date-picker-search\"\n                       data-opens=\"left\"\n                />\n            </div>\n        </td>\n    </tr>\n    <tr>\n        <td class=\"fieldlabel\">\n            ";
echo AdminLang::trans("fields.lineitem");
echo "        </td>\n        <td class=\"fieldarea\">\n            <input type=\"text\" name=\"lineitem\" class=\"form-control input-300\" value=\"";
echo $lineitem;
echo "\">\n        </td>\n        <td width=\"15%\" class=\"fieldlabel\">\n            ";
echo AdminLang::trans("fields.duedate");
echo "        </td>\n        <td class=\"fieldarea\">\n            <div class=\"form-group date-picker-prepend-icon\">\n                <label for=\"inputDueDate\" class=\"field-icon\">\n                    <i class=\"fal fa-calendar-alt\"></i>\n                </label>\n                <input id=\"inputDueDate\"\n                       type=\"text\"\n                       name=\"duedate\"\n                       value=\"";
echo $duedate;
echo "\"\n                       class=\"form-control date-picker-search\"\n                       data-opens=\"left\"\n                />\n            </div>\n        </td>\n    </tr>\n    <tr>\n        <td class=\"fieldlabel\">\n            ";
echo AdminLang::trans("fields.paymentmethod");
echo "        </td>\n        <td class=\"fieldarea\">\n            ";
echo paymentMethodsSelection(AdminLang::trans("global.any"));
echo "        </td>\n        <td width=\"15%\" class=\"fieldlabel\">\n            ";
echo AdminLang::trans("fields.datepaid");
echo "        </td>\n        <td class=\"fieldarea\">\n            <div class=\"form-group date-picker-prepend-icon\">\n                <label for=\"inputDatePaid\" class=\"field-icon\">\n                    <i class=\"fal fa-calendar-alt\"></i>\n                </label>\n                <input id=\"inputDatePaid\"\n                       type=\"text\"\n                       name=\"datepaid\"\n                       value=\"";
echo $datepaid;
echo "\"\n                       class=\"form-control date-picker-search\"\n                       data-opens=\"left\"\n                />\n            </div>\n        </td>\n    </tr>\n    <tr>\n        <td class=\"fieldlabel\">\n            ";
echo AdminLang::trans("fields.status");
echo "        </td>\n        <td class=\"fieldarea\">\n            <select name=\"status\" class=\"form-control select-inline\">\n                <option value=\"\">\n                    ";
echo AdminLang::trans("global.any");
echo "                </option>\n                <option value=\"Draft\"";
echo $status == "Draft" ? " selected" : "";
echo ">\n                    ";
echo AdminLang::trans("status.draft");
echo "                </option>\n                <option value=\"Unpaid\"";
echo $status == "Unpaid" ? " selected" : "";
echo ">\n                    ";
echo AdminLang::trans("status.unpaid");
echo "                </option>\n                <option value=\"Overdue\"";
echo $status == "Overdue" ? " selected" : "";
echo ">\n                    ";
echo AdminLang::trans("status.overdue");
echo "                </option>\n                <option value=\"Paid\"";
echo $status == "Paid" ? " selected" : "";
echo ">\n                    ";
echo AdminLang::trans("status.paid");
echo "                </option>\n                <option value=\"Cancelled\"";
echo $status == "Cancelled" ? " selected" : "";
echo ">\n                    ";
echo AdminLang::trans("status.cancelled");
echo "                </option>\n                <option value=\"Refunded\"";
echo $status == "Refunded" ? " selected" : "";
echo ">\n                    ";
echo AdminLang::trans("status.refunded");
echo "                </option>\n                <option value=\"Collections\"";
echo $status == "Collections" ? " selected" : "";
echo ">\n                    ";
echo AdminLang::trans("status.collections");
echo "                </option>\n                <option value=\"Payment Pending\"";
echo $status == "Payment Pending" ? " selected" : "";
echo ">\n                    ";
echo AdminLang::trans("status.paymentpending");
echo "                </option>\n            </select>\n        </td>\n        <td class=\"fieldlabel\">\n            ";
echo AdminLang::trans("fields.lastCaptureAttempt");
echo "        </td>\n        <td class=\"fieldarea\">\n            <div class=\"form-group date-picker-prepend-icon\">\n                <label for=\"inputLastCaptureAttempt\" class=\"field-icon\">\n                    <i class=\"fal fa-calendar-alt\"></i>\n                </label>\n                <input id=\"inputLastCaptureAttempt\"\n                       type=\"text\"\n                       name=\"last_capture_attempt\"\n                       value=\"";
echo $last_capture_attempt;
echo "\"\n                       class=\"form-control date-picker-search\"\n                       data-opens=\"left\"\n                />\n            </div>\n        </td>\n    </tr>\n    <tr>\n        <td class=\"fieldlabel\" rowspan=\"2\">\n            ";
echo AdminLang::trans("fields.totaldue");
echo "        </td>\n        <td class=\"fieldarea\">\n            ";
echo AdminLang::trans("filters.from");
echo ":\n            <input type=\"text\"\n                   name=\"totalfrom\"\n                   class=\"form-control input-135 input-inline\"\n                   value=\"";
echo $totalFrom;
echo "\"\n            >\n        </td>\n        <td class=\"fieldlabel\">\n            ";
echo AdminLang::trans("fields.dateRefunded");
echo "        </td>\n        <td class=\"fieldarea\">\n            <div class=\"form-group date-picker-prepend-icon\">\n                <label for=\"inputDateRefunded\" class=\"field-icon\">\n                    <i class=\"fal fa-calendar-alt\"></i>\n                </label>\n                <input id=\"inputDateRefunded\"\n                       type=\"text\"\n                       name=\"date_refunded\"\n                       value=\"";
echo $date_refunded;
echo "\"\n                       class=\"form-control date-picker-search\"\n                       data-opens=\"left\"\n                />\n            </div>\n        </td>\n    </tr>\n    <tr>\n        <td class=\"fieldarea\">\n            ";
echo AdminLang::trans("filters.to");
echo ":\n            <input type=\"text\"\n                   name=\"totalto\"\n                   class=\"form-control input-135 input-inline\"\n                   value=\"";
echo $totalTo;
echo "\"\n            >\n        </td>\n        <td class=\"fieldlabel\">\n            ";
echo AdminLang::trans("fields.dateCancelled");
echo "        </td>\n        <td class=\"fieldarea\">\n            <div class=\"form-group date-picker-prepend-icon\">\n                <label for=\"inputDateCancelled\" class=\"field-icon\">\n                    <i class=\"fal fa-calendar-alt\"></i>\n                </label>\n                <input id=\"inputDateCancelled\"\n                       type=\"text\"\n                       name=\"date_cancelled\"\n                       value=\"";
echo $date_cancelled;
echo "\"\n                       class=\"form-control date-picker-search\"\n                       data-opens=\"left\"\n                />\n            </div>\n        </td>\n    </tr>\n</table>\n\n</form>\n\n<br />\n\n";
$gatewaysarray = getGatewaysArray();
$aInt->sortableTableInit("duedate", "DESC");
$numrows = $invoices->count();
$invoices->select(["*", WHMCS\Database\Capsule::raw("(credit + total) as creditTotalSum")])->offset($page * $limit)->limit($limit);
if($orderby === "id") {
    $invoices->orderBy("tblinvoices.invoicenum", $order)->orderBy("tblinvoices.id", $order);
} elseif($orderby === "total") {
    $invoices->orderBy("creditTotalSum", $order);
} else {
    $invoices->orderBy($orderby, $order);
}
$deleteText = AdminLang::trans("global.delete");
$editText = AdminLang::trans("global.edit");
$viewInvoiceText = AdminLang::trans("global.view");
foreach ($invoices->get() as $data) {
    $id = $data["id"];
    $invoicenum = $data["invoicenum"];
    $date = $data["date"];
    $duedate = $data["duedate"];
    $carbonDatePaid = $data["datepaid"];
    $date = $date->toAdminDateFormat();
    $duedate = $duedate->toAdminDateFormat();
    $datepaid = $carbonDatePaid->toAdminDateFormat();
    if($carbonDatePaid->isEmpty()) {
        $datepaid = "-";
    }
    $credit = $data["credit"];
    $total = $data["total"];
    $paymentmethod = $data["paymentmethod"];
    $paymentmethod = $gatewaysarray[$paymentmethod];
    $status = $data["status"];
    $status = getInvoiceStatusColour($status, false);
    $total = formatCurrency($data["creditTotalSum"]);
    if(!$invoicenum) {
        $invoicenum = $id;
    }
    $payments = $data->transactions()->count();
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
        $buttonGroup[] = "<a id=\"viewInvoice" . $id . "\" href=\"" . $data->getAdminViewLink() . "\" " . "class=\"btn btn-default btn-xs\">" . $viewInvoiceText . "</a>";
        $buttonGroup[] = "<a id=\"editInvoice" . $id . "\" href=\"" . $data->getEditInvoiceUrl() . "\" " . "class=\"btn btn-default btn-xs\">" . $editText . "</a>";
    } elseif($aInt->hasPermission("View Invoice")) {
        $buttonGroup[] = "<a id=\"viewInvoice" . $id . "\" href=\"" . $data->getAdminViewLink() . "\" " . "class=\"btn btn-default btn-xs\">" . $viewInvoiceText . "</a>";
    }
    if($aInt->hasPermission("Delete Invoice")) {
        $buttonGroup[] = "<button id=\"deleteInvoice" . $id . "\" type=\"button\" " . "onclick=\"openInvoiceModal('" . $confirmationModal . "', " . $id . ")\"" . " class=\"btn btn-danger btn-xs\">" . $deleteText . "</button>";
    }
    $buttonGroup = implode("", $buttonGroup);
    $tooltipUri = routePath("admin-billing-view-invoice-tooltip", $id, generate_token("plain"));
    $invoiceTooltipLink = "<a href=\"" . $tooltipUri . "\" class=\"invtooltip\" lang=\"\">" . $total . "</a>";
    $tabledata[] = ["<input type=\"checkbox\" name=\"selectedinvoices[]\" value=\"" . $id . "\" class=\"checkall\">", "<a href=\"" . $data->getAdminViewLink() . "\">" . $invoicenum . "</a>", $date, $duedate, $datepaid, $invoiceTooltipLink, $paymentmethod, $status, "<div class=\"btn-group btn-group-xs\">" . $buttonGroup . "</div>"];
}
$tableformurl = $_SERVER["PHP_SELF"] . "?userid=" . $userid . "&filter=1";
if($page) {
    $tableformurl .= "&page=" . $page;
}
$diJavascript = sprintf("onclick=\"return confirm('%s')\"", $aInt->lang("invoices", "duplicateinvoiceconfirm", "1"));
$diClassDisabled = "";
if(!checkPermission("Create Invoice", true)) {
    $diClassDisabled = " disabled";
    $diJavascript = sprintf("aria-disabled=\"true\" data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"%s\" onclick=\"return false;\"", addslashes(AdminLang::trans("permissions.missingPerm", [":perm" => "Create Invoice"])));
}
$mcClassDisabled = "";
$cancelConfirm = addslashes(AdminLang::trans("invoices.markcancelledconfirm"));
$mcJavascript = "onclick=\"return confirm('" . $cancelConfirm . "')\"";
if(!$aInt->hasPermission("Cancel Invoice")) {
    $mcClassDisabled = " disabled";
    $mcJavascript = sprintf("aria-disabled=\"true\" data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"%s\" disabled ", addslashes(AdminLang::trans("permissions.missingPerm", [":perm" => AdminLang::trans("permissions.159")])));
}
$markCancelledText = AdminLang::trans("invoices.markcancelled");
$tableformbuttons = "<input type=\"submit\" value=\"" . $aInt->lang("invoices", "markpaid") . "\" class=\"btn btn-success\" name=\"markpaid\" onclick=\"return confirm('" . $aInt->lang("invoices", "markpaidconfirm", "1") . "')\" />\n<input type=\"submit\" value=\"" . $aInt->lang("invoices", "markunpaid") . "\" class=\"btn btn-default\" name=\"markunpaid\" onclick=\"return confirm('" . $aInt->lang("invoices", "markunpaidconfirm", "1") . "')\" />\n<input type=\"submit\"\n       value=\"" . $markCancelledText . "\"\n       class=\"btn btn-default" . $mcClassDisabled . "\"\n       name=\"markcancelled\"\n       " . $mcJavascript . "    \n/>\n<input type=\"submit\" value=\"" . $aInt->lang("invoices", "duplicateinvoice") . "\" class=\"btn btn-default" . $diClassDisabled . "\" name=\"duplicateinvoice\" " . $diJavascript . " />\n<input type=\"submit\" value=\"" . $aInt->lang("invoices", "sendreminder") . "\" class=\"btn btn-default\" name=\"paymentreminder\" onclick=\"return confirm('" . $aInt->lang("invoices", "sendreminderconfirm", "1") . "')\" />\n<input type=\"submit\" value=\"" . $aInt->lang("invoices", "merge") . "\" class=\"btn btn-default\" name=\"merge\" onclick=\"return confirm('" . $aInt->lang("invoices", "mergeconfirm", "1") . "')\" />\n<input type=\"submit\" value=\"" . $aInt->lang("invoices", "masspay") . "\" class=\"btn btn-default\" name=\"masspay\" onclick=\"return confirm('" . $aInt->lang("invoices", "masspayconfirm", "1") . "')\" />\n<input type=\"submit\" value=\"" . $aInt->lang("global", "delete") . "\" class=\"btn btn-danger\" name=\"massdelete\" onclick=\"return confirm('" . $aInt->lang("invoices", "massdeleteconfirm", "1") . "')\" />";
unset($diClassDisabled);
unset($diJavascript);
unset($markCancelledText);
unset($mcClassDisabled);
unset($mcJavascript);
echo $aInt->sortableTable(["checkall", ["id", AdminLang::trans("fields.invoicenum")], ["date", AdminLang::trans("fields.invoicedate")], ["duedate", AdminLang::trans("fields.duedate")], ["datepaid", AdminLang::trans("fields.datepaid")], ["total", AdminLang::trans("fields.total")], ["paymentmethod", AdminLang::trans("fields.paymentmethod")], ["status", AdminLang::trans("fields.status")], ["", "&nbsp;", "150"]], $tabledata, $tableformurl, $tableformbuttons);
echo $aInt->modal("DeleteInvoice", AdminLang::trans("invoices.deleteTitle"), "<p>" . AdminLang::trans("invoices.delete") . "</p>" . "<p>" . AdminLang::trans("invoices.deleteConfirm") . "</p>", [["title" => AdminLang::trans("global.delete"), "onclick" => "doDeleteCall()"], ["title" => AdminLang::trans("global.cancel")]]);
echo $aInt->modal("ExistingCreditAndPayments", AdminLang::trans("invoices.existingCreditPaymentsTitle"), "<p>" . AdminLang::trans("invoices.delete") . "</p>" . "<p>" . AdminLang::trans("invoices.existingCredit") . "</p>" . "<p>" . AdminLang::trans("invoices.existingPayments") . "</p>" . "<p>" . AdminLang::trans("invoices.deleteConfirm") . "</p>", [["title" => AdminLang::trans("invoices.existingCreditPaymentsReturn"), "onclick" => "doDeleteCall(\"returnCredit\")"], ["title" => AdminLang::trans("invoices.existingCreditPaymentsDiscard"), "onclick" => "doDeleteCall()"], ["title" => AdminLang::trans("global.cancel")]]);
echo $aInt->modal("ExistingCredit", AdminLang::trans("invoices.existingCreditTitle"), "<p>" . AdminLang::trans("invoices.delete") . "</p>" . "<p>" . AdminLang::trans("invoices.existingCredit") . "</p>" . "<p>" . AdminLang::trans("invoices.deleteConfirm") . "</p>", [["title" => AdminLang::trans("invoices.existingCreditReturn"), "onclick" => "doDeleteCall(\"returnCredit\")"], ["title" => AdminLang::trans("invoices.existingCreditDiscard"), "onclick" => "doDeleteCall()"], ["title" => AdminLang::trans("global.cancel")]]);
echo $aInt->modal("ExistingPayments", AdminLang::trans("invoices.existingPaymentsTitle"), "<p>" . AdminLang::trans("invoices.delete") . "</p>" . "<p>" . AdminLang::trans("invoices.existingPayments") . "</p>" . "<p>" . AdminLang::trans("invoices.deleteConfirm") . "</p>", [["title" => AdminLang::trans("invoices.existingPaymentsOrphan"), "onclick" => "doDeleteCall()"], ["title" => AdminLang::trans("global.cancel")]]);
$token = generate_token("link");
$jsCode = "var invoice = 0;\nfunction openInvoiceModal(displayModal, invoiceID) {\n    /**\n     * Store the invoiceID in the global JS variable\n     */\n    invoice = invoiceID;\n    \$('#modal' + displayModal).modal('show');\n}\n\nfunction doDeleteCall(credit) {\n    var deleteUrl = '" . $whmcs->getPhpSelf() . "?userid=" . $userid . "&delete=true';\n    if (credit == 'returnCredit') {\n        deleteUrl = deleteUrl + '&returnCredit=true&invoiceid=';\n    } else {\n        deleteUrl = deleteUrl + '&invoiceid=';\n    }\n    window.location = deleteUrl + invoice + '" . $token . "';\n}";
$jquerycode .= "jQuery(document).ready(function() {\n    jQuery('#sortabletbl1').find('tr').addClass('text-center');\n});";
$content = ob_get_contents();
ob_end_clean();
$aInt->content = $content;
$aInt->jquerycode = $jquerycode;
$aInt->jscode = $jsCode;
$aInt->display();

?>