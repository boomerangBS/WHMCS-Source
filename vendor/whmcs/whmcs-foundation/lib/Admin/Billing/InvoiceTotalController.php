<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Admin\Billing;

class InvoiceTotalController
{
    public function calculate(\WHMCS\Http\Message\ServerRequest $request)
    {
        $invoiceId = $request->get("invoiceId");
        $invoice = \WHMCS\Billing\Invoice::find($invoiceId);
        if($invoice === NULL) {
            return new \WHMCS\Http\Message\JsonResponse(["invoiceTotal" => NULL]);
        }
        $invoiceAmount = $invoice->invoiceAmount;
        $balance = $invoice->balance;
        $invoiceItems = collect($request->get("items"))->filter(function ($item) {
            return isset($item["description"]) && isset($item["amount"]) && isset($item["taxed"]);
        })->map(function ($item) {
            return new \WHMCS\Billing\Invoice\Item($item);
        });
        $invoice->setRelation("items", $invoiceItems);
        $invoice->calculateInvoiceTotal();
        return new \WHMCS\Http\Message\JsonResponse(["invoiceTotal" => ["total" => formatCurrency($invoice->total), "subtotal" => formatCurrency($invoice->subtotal), "credit" => formatCurrency($invoice->credit), "tax" => formatCurrency($invoice->tax), "tax2" => formatCurrency($invoice->tax2), "invoiceAmount" => formatCurrency($invoiceAmount), "balance" => formatCurrency($balance)]]);
    }
}

?>