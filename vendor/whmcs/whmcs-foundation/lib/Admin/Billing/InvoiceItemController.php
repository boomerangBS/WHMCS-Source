<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Admin\Billing;

class InvoiceItemController
{
    public function destroy(\WHMCS\Http\Message\ServerRequest $request)
    {
        $invoiceItemId = $request->get("invoiceItemId");
        if(filter_var($invoiceItemId, FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]]) === false) {
            return new \WHMCS\Http\Message\JsonResponse(["success" => false, "errorMessage" => \AdminLang::trans("notifications.invoice.itemNotFound")]);
        }
        $invoiceItem = \WHMCS\Billing\Invoice\Item::with("invoice")->find($invoiceItemId);
        if($invoiceItem === NULL) {
            return new \WHMCS\Http\Message\JsonResponse(["success" => false, "errorMessage" => \AdminLang::trans("notifications.invoice.itemNotFound")]);
        }
        \WHMCS\Database\Capsule::transaction(function () use($invoiceItem) {
            $invoiceItem->delete();
            $invoiceItem->invoice->updateInvoiceTotal();
        });
        return new \WHMCS\Http\Message\JsonResponse(["success" => true]);
    }
}

?>