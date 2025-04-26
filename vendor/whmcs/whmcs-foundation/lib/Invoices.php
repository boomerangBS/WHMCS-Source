<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

namespace WHMCS;

// Decoded file for php version 72.
class Invoices extends TableModel
{
    protected static $invoiceStatusValues;
    public function _execute(array $implementationData = [])
    {
        return $this->getInvoices($implementationData);
    }
    public function getInvoices($criteria = [])
    {
        global $aInt;
        global $currency;
        $joinClient = "LEFT JOIN tblclients ON tblclients.id=tblinvoices.userid";
        $query = "SELECT tblinvoices.*, tblclients.firstname, tblclients.lastname, tblclients.companyname, tblclients.groupid, tblclients.currency" . " FROM tblinvoices IGNORE INDEX (`status`) " . $joinClient;
        $totalsQuery = "SELECT COUNT(*) FROM tblinvoices IGNORE INDEX (`status`)";
        if(!empty($criteria["clientid"]) || !empty($criteria["clientname"])) {
            $totalsQuery .= "  " . $joinClient;
        }
        $filters = $this->buildCriteria($criteria);
        if(!empty($filters)) {
            $query .= " WHERE " . implode(" AND ", $filters);
            $totalsQuery .= " WHERE " . implode(" AND ", $filters);
        }
        $result = full_query($totalsQuery);
        $data = mysql_fetch_array($result);
        $this->getPageObj()->setNumResults($data[0]);
        $gateways = new Gateways();
        $gatewaysAndTypes = Module\GatewaySetting::getActiveGatewayTypes();
        $orderby = $this->getPageObj()->getOrderBy();
        $sortDirection = $this->getPageObj()->getSortDirection();
        if($orderby == "clientname") {
            $orderby = "firstname " . $sortDirection . ",lastname " . $sortDirection . ",companyname";
        }
        if($orderby == "id") {
            $orderby = "tblinvoices.invoicenum " . $sortDirection . ",tblinvoices.id";
        }
        $invoices = [];
        $query = $query . " ORDER BY " . $orderby . " " . $sortDirection . " LIMIT " . $this->getQueryLimit();
        $result = full_query($query);
        while ($data = mysql_fetch_array($result)) {
            $id = $data["id"];
            $invoicenum = $data["invoicenum"];
            $userid = $data["userid"];
            $date = $data["date"];
            $duedate = $data["duedate"];
            $subtotal = $data["subtotal"];
            $credit = $data["credit"];
            $total = $data["total"];
            $gateway = $data["paymentmethod"];
            $status = $data["status"];
            $lastCaptureAttempt = $data["last_capture_attempt"];
            $firstname = $data["firstname"];
            $lastname = $data["lastname"];
            $companyname = $data["companyname"];
            $groupid = $data["groupid"];
            $currency = $data["currency"];
            $clientname = $aInt->outputClientLink($userid, $firstname, $lastname, $companyname, $groupid);
            $paymentmethod = $gateways->getDisplayName($gateway);
            $currency = getCurrency(NULL, $currency);
            $totalformatted = formatCurrency($credit + $total);
            $statusformatted = $this->formatStatus($status);
            $date = fromMySQLDate($date);
            $duedate = fromMySQLDate($duedate);
            $lastCaptureAttempt = $gatewaysAndTypes[$gateway] == Module\Gateway::GATEWAY_CREDIT_CARD ? $lastCaptureAttempt != "0000-00-00 00:00:00" ? fromMySQLDate($lastCaptureAttempt) : "-" : \AdminLang::trans("global.na");
            if(!$invoicenum) {
                $invoicenum = $id;
            }
            $baseUrl = Utility\Environment\WebHelper::getBaseUrl();
            $adminFolder = \App::get_admin_folder_name();
            $invoices[] = ["id" => $id, "invoicenum" => $invoicenum, "userid" => $userid, "clientname" => $clientname, "date" => $date, "duedate" => $duedate, "lastCaptureAttempt" => $lastCaptureAttempt, "subtotal" => $subtotal, "credit" => $credit, "total" => $total, "totalformatted" => $totalformatted, "gateway" => $gateway, "paymentmethod" => $paymentmethod, "status" => $status, "statusformatted" => $statusformatted, "viewLink" => fqdnRoutePath("admin-billing-view-invoice", $id), "editLink" => $baseUrl . "/" . $adminFolder . "/invoices.php?action=edit&id=" . $id];
        }
        return $invoices;
    }
    private function buildCriteria($criteria) : array
    {
        $filters = [];
        if($criteria["clientid"]) {
            $clientId = (int) $criteria["clientid"];
            $filters[] = "userid=" . $clientId;
        }
        if($criteria["clientname"]) {
            $clientName = db_escape_string($criteria["clientname"]);
            $filters[] = "concat(firstname,' ',lastname) LIKE '%" . $clientName . "%'";
        }
        if($criteria["invoicenum"]) {
            $invoiceNum = db_escape_string($criteria["invoicenum"]);
            $filters[] = "(tblinvoices.id='" . $invoiceNum . "' OR tblinvoices.invoicenum='" . $invoiceNum . "')";
        }
        if($criteria["lineitem"]) {
            $lineItem = db_escape_string($criteria["lineitem"]);
            $filters[] = "tblinvoices.id IN (SELECT invoiceid FROM tblinvoiceitems" . " WHERE description LIKE '%" . $lineItem . "%')";
        }
        if($criteria["paymentmethod"]) {
            $paymentMethod = db_escape_string($criteria["paymentmethod"]);
            $filters[] = "tblinvoices.paymentmethod='" . $paymentMethod . "'";
        }
        $dateFilters = ["invoicedate" => "date", "duedate" => "duedate", "datepaid" => "datepaid", "last_capture_attempt" => "last_capture_attempt", "date_refunded" => "date_refunded", "date_cancelled" => "date_cancelled"];
        foreach ($dateFilters as $filterCriteria => $fieldName) {
            if(array_key_exists($filterCriteria, $criteria) && $criteria[$filterCriteria]) {
                $dateRange = $criteria[$filterCriteria];
                $dateRange = Carbon::parseDateRangeValue($dateRange);
                $dateFrom = $dateRange["from"];
                $dateTo = $dateRange["to"];
                $filters[] = "tblinvoices." . $fieldName . " BETWEEN '" . $dateFrom->toDateTimeString() . "'" . " AND '" . $dateTo->toDateTimeString() . "'";
            }
        }
        if($criteria["totalfrom"]) {
            $totalFrom = db_escape_string($criteria["totalfrom"]);
            $filters[] = "tblinvoices.total>='" . $totalFrom . "'";
        }
        if($criteria["totalto"]) {
            $totalTo = db_escape_string($criteria["totalto"]);
            $filters[] = "tblinvoices.total<='" . $totalTo . "'";
        }
        if($criteria["status"]) {
            if($criteria["status"] == "Overdue") {
                $overdueDate = date("Ymd");
                $unpaid = Utility\Status::UNPAID;
                $filters[] = "tblinvoices.status='" . $unpaid . "' AND tblinvoices.duedate<'" . $overdueDate . "'";
            } else {
                $status = db_escape_string($criteria["status"]);
                $filters[] = "tblinvoices.status='" . $status . "'";
            }
        }
        return $filters;
    }
    public function formatStatus($status)
    {
        if(defined("ADMINAREA")) {
            global $aInt;
            if($status == "Draft") {
                $status = "<span class=\"textgrey\">" . $aInt->lang("status", "draft") . "</span>";
            } elseif($status == "Unpaid") {
                $status = "<span class=\"textred\">" . $aInt->lang("status", "unpaid") . "</span>";
            } elseif($status == "Paid") {
                $status = "<span class=\"textgreen\">" . $aInt->lang("status", "paid") . "</span>";
            } elseif($status == "Cancelled") {
                $status = "<span class=\"textgrey\">" . $aInt->lang("status", "cancelled") . "</span>";
            } elseif($status == "Refunded") {
                $status = "<span class=\"textblack\">" . $aInt->lang("status", "refunded") . "</span>";
            } elseif($status == "Collections") {
                $status = "<span class=\"textgold\">" . $aInt->lang("status", "collections") . "</span>";
            } elseif($status == "Payment Pending") {
                $status = "<span class=\"textgreen\">" . \AdminLang::trans("status.paymentpending") . "</span>";
            } else {
                $status = "Unrecognised";
            }
        } else {
            global $_LANG;
            if($status == "Unpaid") {
                $status = "<span class=\"textred\">" . $_LANG["invoicesunpaid"] . "</span>";
            } elseif($status == "Paid") {
                $status = "<span class=\"textgreen\">" . $_LANG["invoicespaid"] . "</span>";
            } elseif($status == "Cancelled") {
                $status = "<span class=\"textgrey\">" . $_LANG["invoicescancelled"] . "</span>";
            } elseif($status == "Refunded") {
                $status = "<span class=\"textblack\">" . $_LANG["invoicesrefunded"] . "</span>";
            } elseif($status == "Collections") {
                $status = "<span class=\"textgold\">" . $_LANG["invoicescollections"] . "</span>";
            } elseif($status == "Payment Pending") {
                $status = "<span class=\"textgreen\">" . $_LANG["invoicesPaymentPending"] . "</span>";
            } else {
                $status = "Unrecognised";
            }
        }
        return $status;
    }
    public function getInvoiceTotals()
    {
        global $currency;
        $invoicesummary = [];
        $result = full_query("SELECT currency,COUNT(tblinvoices.id),SUM(total) FROM tblinvoices INNER JOIN tblclients ON tblclients.id=tblinvoices.userid WHERE tblinvoices.status='Paid' GROUP BY tblclients.currency");
        while ($data = mysql_fetch_array($result)) {
            $invoicesummary[$data[0]]["paid"] = $data[2];
        }
        $result = full_query("SELECT currency,COUNT(tblinvoices.id),SUM(total)-COALESCE(SUM((SELECT SUM(amountin) FROM tblaccounts WHERE tblaccounts.invoiceid=tblinvoices.id)),0) FROM tblinvoices INNER JOIN tblclients ON tblclients.id=tblinvoices.userid WHERE tblinvoices.status='Unpaid' AND tblinvoices.duedate>='" . date("Ymd") . "' GROUP BY tblclients.currency");
        while ($data = mysql_fetch_array($result)) {
            $invoicesummary[$data[0]]["unpaid"] = $data[2];
        }
        $result = full_query("SELECT currency,COUNT(tblinvoices.id),SUM(total)-COALESCE(SUM((SELECT SUM(amountin) FROM tblaccounts WHERE tblaccounts.invoiceid=tblinvoices.id)),0) FROM tblinvoices INNER JOIN tblclients ON tblclients.id=tblinvoices.userid WHERE tblinvoices.status='Unpaid' AND tblinvoices.duedate<'" . date("Ymd") . "' GROUP BY tblclients.currency");
        while ($data = mysql_fetch_array($result)) {
            $invoicesummary[$data[0]]["overdue"] = $data[2];
        }
        $totals = [];
        foreach ($invoicesummary as $currency => $vals) {
            $currency = getCurrency(NULL, $currency);
            if(!isset($vals["paid"])) {
                $vals["paid"] = 0;
            }
            if(!isset($vals["unpaid"])) {
                $vals["unpaid"] = 0;
            }
            if(!isset($vals["overdue"])) {
                $vals["overdue"] = 0;
            }
            $paid = formatCurrency($vals["paid"]);
            $unpaid = formatCurrency($vals["unpaid"]);
            $overdue = formatCurrency($vals["overdue"]);
            $totals[] = ["currencycode" => $currency["code"], "paid" => $paid, "unpaid" => $unpaid, "overdue" => $overdue];
        }
        return $totals;
    }
    public function duplicate($invoiceid)
    {
        $existingInvoice = Billing\Invoice::with("items")->find($invoiceid);
        $newInvoice = $existingInvoice->replicate(["invoicenum", "credit"]);
        $newInvoice->status = "Draft";
        $newInvoice->save();
        $userid = $newInvoice->clientId;
        $newid = $newInvoice->id;
        $newItems = [];
        foreach ($existingInvoice->items as $invoiceItem) {
            $newItems[] = $invoiceItem->replicate();
        }
        $newInvoice->items()->saveMany($newItems);
        $newInvoice->updateInvoiceTotal();
        logActivity("Duplicated Invoice - Existing Invoice ID: " . $invoiceid . " - New Invoice ID: " . $newid, $userid);
        return true;
    }
    public static function isSequentialPaidInvoiceNumberingEnabled()
    {
        $whmcs = Application::getInstance();
        return $whmcs->get_config("SequentialInvoiceNumbering") ? true : false;
    }
    public static function getNextSequentialPaidInvoiceNumber()
    {
        $numberToAssign = Config\Setting::getValue("SequentialInvoiceNumberFormat");
        $nextNumber = Database\Capsule::table("tblconfiguration")->where("setting", "SequentialInvoiceNumberValue")->value("value");
        Config\Setting::setValue("SequentialInvoiceNumberValue", self::padAndIncrement($nextNumber));
        $numberToAssign = str_replace("{YEAR}", date("Y"), $numberToAssign);
        $numberToAssign = str_replace("{MONTH}", date("m"), $numberToAssign);
        $numberToAssign = str_replace("{DAY}", date("d"), $numberToAssign);
        $numberToAssign = str_replace("{NUMBER}", $nextNumber, $numberToAssign);
        return $numberToAssign;
    }
    public static function padAndIncrement($number, $incrementAmount = 1)
    {
        $newNumber = $number + $incrementAmount;
        if(substr($number, 0, 1) == "0") {
            $numberLength = strlen($number);
            $newNumber = str_pad($newNumber, $numberLength, "0", STR_PAD_LEFT);
        }
        return $newNumber;
    }
    public static function adjustIncrementForNextInvoice($lastInvoiceId)
    {
        $incrementValue = (int) Config\Setting::getValue("InvoiceIncrement");
        if(1 < $incrementValue) {
            $incrementedId = $lastInvoiceId + $incrementValue - 1;
            insert_query("tblinvoices", ["id" => $incrementedId]);
            delete_query("tblinvoices", ["id" => $incrementedId]);
        }
    }
    public static function getInvoiceStatusValues()
    {
        return self::$invoiceStatusValues;
    }
}

?>