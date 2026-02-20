<?php

namespace WHMCS\Cron\Task;

class InvoiceAutoCancellation extends \WHMCS\Scheduling\Task\AbstractTask
{
    protected $defaultPriority = 1605;
    protected $defaultFrequency = 1440;
    protected $defaultDescription = "Cancel Overdue Invoices";
    protected $defaultName = "Overdue Invoice Cancellations";
    protected $systemName = "InvoiceAutoCancellation";
    protected $outputs = ["cancelled" => ["defaultValue" => 0, "identifier" => "cancelled", "name" => "Auto-Cancelled"], "action.detail" => ["defaultValue" => "", "identifier" => "action.detail", "name" => "Action Detail"]];
    protected $icon = "far fa-calendar-times";
    protected $disabledIcon = "fas fa-times";
    protected $successCountIdentifier = "cancelled";
    protected $failedCountIdentifier = "";
    protected $successKeyword = "Auto-Cancelled";
    public function __invoke()
    {
        $this->setDetails(["success" => []]);
        $this->processAutoInvoiceCancellations();
        $this->output("cancelled")->write(count($this->getSuccesses()));
        $this->output("action.detail")->write(json_encode($this->getDetail()));
        return $this;
    }
    protected function processInvoice($invoice, string $invoiceNote, string $cancellationDate) : void
    {
        $capsuleQuery = \WHMCS\Database\Capsule::table("tblinvoices")->where([["id", "=", $invoice->id], ["status", "=", \WHMCS\Billing\Invoice::STATUS_UNPAID], ["updated_at", "=", $invoice->updated_at]]);
        if(!$capsuleQuery->exists()) {
            return NULL;
        }
        $hookResults = \HookMgr::run("PreInvoiceAutomaticCancellation", ["invoiceid" => $invoice->id]);
        if(!empty($hookResults["abortCancel"]) && $hookResults["abortCancel"] === true) {
            return NULL;
        }
        $capsuleQuery->update(["status" => \WHMCS\Billing\Invoice::STATUS_CANCELLED, "notes" => empty($invoice->notes) ? $invoiceNote : $invoice->notes . "\n" . $invoiceNote, "date_cancelled" => $cancellationDate]);
        $this->addSuccess(["invoice", $invoice->id]);
        \HookMgr::run("InvoiceCancelled", ["invoiceid" => $invoice->id]);
        logActivity(sprintf("Cron Job: Cancelled Invoice - Invoice ID: %s", $invoice->id));
    }
    protected function processAutoInvoiceCancellations() : void
    {
        if(!$this->isAutoCancellationEnabled()) {
            return NULL;
        }
        $autoCancellationDays = (int) \WHMCS\Config\Setting::getValue("InvoiceAutoCancellationDays");
        $carbonToday = \WHMCS\Carbon::today();
        $cancellationDate = $carbonToday->copy()->subDays($autoCancellationDays)->toDateString();
        $invoiceNote = \AdminLang::trans("invoices.autoCancellation", [":dayCount" => $autoCancellationDays, ":dateTime" => $carbonToday->toAdminDateFormat()]);
        $checkForInvoices = true;
        $queryBuilder = \WHMCS\Database\Capsule::table("tblinvoices")->select("tblinvoices.id", "tblinvoices.credit", "tblinvoices.notes", "tblinvoices.updated_at")->selectRaw("SUM(tblaccounts.amountin) - SUM(tblaccounts.amountout) as balance")->leftJoin("tblaccounts", "tblinvoices.id", "tblaccounts.invoiceid")->where([["tblinvoices.status", "=", \WHMCS\Billing\Invoice::STATUS_UNPAID], ["tblinvoices.duedate", "<=", $cancellationDate], ["tblinvoices.credit", "<=", 0]])->groupBy("tblinvoices.id")->havingRaw("balance <= 0 OR COUNT(tblaccounts.invoiceid) = 0");
        while ($checkForInvoices) {
            $invoicesCollection = $queryBuilder->limit(100)->get();
            if($invoicesCollection->count() <= 0) {
                $checkForInvoices = false;
            } else {
                foreach ($invoicesCollection as $invoice) {
                    $this->processInvoice($invoice, $invoiceNote, $carbonToday->toDateTimeString());
                }
            }
        }
    }
    protected function isAutoCancellationEnabled()
    {
        return \WHMCS\Config\Setting::getValue("InvoiceAutoCancellation") == "on";
    }
    public function hasDetail()
    {
        return $this->isAutoCancellationEnabled();
    }
}

?>