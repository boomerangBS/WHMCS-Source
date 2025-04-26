<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
if($flash) {
    echo WHMCS\View\Helper::alert($flash["text"], $flash["type"]);
}
if($validationErrorMessage) {
    echo $validationErrorMessage;
}
echo "\n<div class=\"pull-right-md-larger\">\n    <a\n        id=\"btnManageInvoice\"\n        class=\"btn btn-default";
echo !$manageInvoicePermission ? " disabled" : "";
echo " btn-sm\"\n        href=\"";
echo $manageInvoicePermission ? $webroot . "/invoices.php?action=edit&id=" . $invoice->id : "#";
echo "\"\n    >\n        <i aria-hidden=\"true\" class=\"fas fa-edit fa-fw\"></i>\n        ";
echo AdminLang::trans("invoices.manageInvoice");
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
echo $aInt->beginAdminTabs($tabs, true);
echo "\n";
$this->insert("billing/invoice/partials/view/tab-summary");
$this->insert("billing/invoice/partials/view/tab-add-payment");
$this->insert("billing/invoice/partials/view/tab-credit");
$this->insert("billing/invoice/partials/view/tab-refund");
$this->insert("billing/invoice/partials/view/tab-notes");
echo "\n";
echo $aInt->endAdminTabs();
echo "\n<h2>";
echo AdminLang::trans("invoices.items");
echo "</h2>\n\n<div class=\"tablebg table-responsive\">\n    <table id=\"tableInvoiceItems\" class=\"datatable\" style=\"width: 100%;border: 0;border-spacing: 1px;padding: 3px;\">\n        <tr>\n            <th>";
echo AdminLang::trans("fields.description");
echo "</th>\n            <th style=\"width: 120px;\">";
echo AdminLang::trans("fields.amount");
echo "</th>\n            <th style=\"width: 70px;\">";
echo AdminLang::trans("fields.taxed");
echo "</th>\n        </tr>\n        ";
foreach ($invoice->items as $item) {
    echo "            <tr>\n                <td id=\"description";
    echo $item->id;
    echo "\">\n                    <span>";
    echo nl2br($item->description);
    echo "</span>\n                </td>\n                <td class=\"text-center\" id=\"amount";
    echo $item->id;
    echo "\">\n                    ";
    echo formatCurrency($item->amount, $invoice->client->currencyId);
    echo "                </td>\n                <td class=\"text-center\" id=\"taxed";
    echo $item->id;
    echo "\">\n                    ";
    echo $item->taxed ? "<i class=\"fas fa-fw fa-check text-success\"></i>" : "";
    echo "                </td>\n            </tr>\n        ";
}
echo "        <tr>\n            <td style=\"text-align:right;background-color:#efefef;\">\n                <strong>";
echo AdminLang::trans("fields.subtotal");
echo ":</strong>&nbsp;\n            </td>\n            <td style=\"background-color:#efefef;text-align:center;\">\n                <strong>";
echo formatCurrency($invoice->subtotal, $invoice->client->currencyId);
echo "</strong>\n            </td>\n            <td style=\"background-color:#efefef;text-align:center;\"></td>\n        </tr>\n        ";
if($taxEnabled) {
    echo "            ";
    if(!valueIsZero($invoice->taxRate1)) {
        echo "                <tr>\n                    <td style=\"text-align:right;background-color:#efefef;\">\n                        ";
        echo $invoice->taxRate1;
        echo "% ";
        echo $taxData["name"] ?: AdminLang::trans("invoices.taxdue");
        echo ": &nbsp;\n                    </td>\n                    <td style=\"background-color:#efefef;text-align:center;\">\n                        ";
        echo formatCurrency($invoice->tax1, $invoice->client->currencyId);
        echo "                    </td>\n                    <td style=\"background-color:#efefef;text-align:center;\"></td>\n                </tr>\n            ";
    }
    echo "            ";
    if(!valueIsZero($invoice->taxRate2)) {
        echo "            <tr>\n                <td style=\"text-align:right;background-color:#efefef;\">\n                    ";
        echo $invoice->taxRate2;
        echo "% ";
        echo $taxData2["name"] ?: AdminLang::trans("invoices.taxdue");
        echo ": &nbsp;\n                </td>\n                <td style=\"background-color:#efefef;text-align:center;\">\n                    ";
        echo formatCurrency($invoice->tax2, $invoice->client->currencyId);
        echo "                </td>\n                <td style=\"background-color:#efefef;text-align:center;\"></td>\n            </tr>\n            ";
    }
    echo "        ";
}
echo "        <tr>\n            <td style=\"text-align:right;background-color:#efefef;\">\n                ";
echo AdminLang::trans("fields.credit");
echo ":&nbsp;\n            </td>\n            <td style=\"background-color:#efefef;text-align:center;\">\n                ";
echo formatCurrency($invoice->credit, $invoice->client->currencyId);
echo "            </td>\n            <td style=\"background-color:#efefef;text-align:center;\"></td>\n        </tr>\n        <tr>\n            <th style=\"text-align:right;\">\n                ";
echo AdminLang::trans("fields.totaldue");
echo ":&nbsp;\n            </th>\n            <th>";
echo formatCurrency($invoice->total, $invoice->client->currencyId);
echo "</th>\n            <th></th>\n        </tr>\n    </table>\n</div>\n\n<h2>";
echo AdminLang::trans("invoices.transactions");
echo "</h2>\n\n";
$aInt->sortableTableInit("nopagination");
echo "<div class=\"table-responsive\">";
echo $aInt->sortableTable([AdminLang::trans("fields.date"), AdminLang::trans("fields.paymentmethod"), AdminLang::trans("fields.transid"), AdminLang::trans("fields.amount"), AdminLang::trans("fields.fees")], $transactionTableData);
echo "</div><h2>";
echo AdminLang::trans("invoices.transactionsHistory");
echo "</h2><div class=\"table-responsive\">";
echo $aInt->sortableTable([AdminLang::trans("fields.date"), AdminLang::trans("fields.paymentmethod"), AdminLang::trans("fields.transid"), AdminLang::trans("fields.status"), AdminLang::trans("fields.description")], $transactionHistoryTableData);
echo "</div>";
if($affiliateHistoryTableData) {
    echo "<h2>";
    echo AdminLang::trans("affiliates.commissionshistory");
    echo "</h2><div class=\"table-responsive\">";
    echo $aInt->sortableTable([AdminLang::trans("fields.date"), AdminLang::trans("fields.affiliate"), AdminLang::trans("fields.amount"), AdminLang::trans("fields.description")], $affiliateHistoryTableData);
    echo "</div>";
}
if($invoice->status !== $invoice::STATUS_CANCELLED && $invoice->status !== $invoice::STATUS_DRAFT) {
    echo $aInt->modal("DuplicateTransaction", AdminLang::trans("transactions.duplicateTransaction"), AdminLang::trans("transactions.forceDuplicateTransaction"), [["title" => AdminLang::trans("global.continue"), "onclick" => "addInvoicePayment();return false;", "class" => "btn-danger"], ["title" => AdminLang::trans("global.cancel"), "onclick" => "cancelAddPayment();return false;"]]);
}

?>