<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Payment\Observer;

class CreditClientForInvoiceOverpayments implements ObserverInterface
{
    public function observe(\WHMCS\Payment\Event\InvoiceOverpayment $event) : void
    {
        $remainderAfterPayment = $this->remainderAfterPayment($event->preTransactionBalance(), $event->transactionAmount()->amount());
        if($remainderAfterPayment < 0) {
            $fundsForThisInvoiceFromOtherPayments = $this->getFundsToApplyToInvoice($event->invoice());
            $creditAmount = abs($remainderAfterPayment + $fundsForThisInvoiceFromOtherPayments);
            $this->createCreditLedgerEntryForClientOfInvoice($event->invoice(), $event->date(), $creditAmount);
            $this->creditClientOfInvoice($event->invoice(), $creditAmount);
        }
    }
    private function remainderAfterPayment(\WHMCS\Payment\Contracts\MonetaryAmountInterface $startBalance, \WHMCS\Payment\Contracts\MonetaryAmountInterface $paymentAmount)
    {
        $invoiceBalanceBeforePayment = $startBalance->value();
        $paymentOfTransaction = $paymentAmount->value();
        $balanceAmount = $invoiceBalanceBeforePayment - $paymentOfTransaction;
        if(valueIsZero($balanceAmount)) {
            $balanceAmount = 0;
        }
        return $balanceAmount;
    }
    private function getFundsToApplyToInvoice(\WHMCS\Billing\Invoice $invoice) : \WHMCS\Billing\Invoice
    {
        return (double) \WHMCS\Database\Capsule::table("tblcredit")->where("relid", $invoice->id)->sum("amount");
    }
    private function createCreditLedgerEntryForClientOfInvoice(\WHMCS\Billing\Invoice $invoice, \WHMCS\Carbon $date, $amount)
    {
        $invoiceId = $invoice->id;
        $client = $invoice->client;
        \WHMCS\Database\Capsule::table("tblcredit")->insert(["clientid" => $client->id, "date" => $date->toDateTimeString(), "description" => "Invoice #" . $invoiceId . " Overpayment", "amount" => $amount, "relid" => $invoiceId]);
    }
    private function creditClientOfInvoice(\WHMCS\Billing\Invoice $invoice, $amount)
    {
        $client = $invoice->client;
        $client->credit += $amount;
        $client->save();
    }
}

?>