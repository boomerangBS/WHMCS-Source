<?php

echo "\n";
echo $aInt->nextAdminTab();
if($refundPermission && $refundTransactions->count()) {
    echo "<form method=\"post\"\n      id=\"transactions\"\n      action=\"";
    echo fqdnRoutePath("admin-billing-view-invoice-refund", $invoice->id);
    echo "\"\n    ";
    echo $refundOnSubmit;
    echo ">\n\n    <table class=\"form\" width=\"100%\" border=\"0\" cellspacing=\"2\" cellpadding=\"3\">\n        <tr>\n            <td width=\"20%\" class=\"fieldlabel\">\n                ";
    echo AdminLang::trans("invoices.transactions");
    echo "            </td>\n            <td class=\"fieldarea\">\n                <select id=\"transid\" name=\"transaction_id\" class=\"form-control select-inline\">\n                    ";
    foreach ($refundTransactions as $transaction) {
        echo "<option value=\"" . $transaction->id . "\" data-amount=\"" . $transaction->amountIn . "\">\n" . $transaction->date->toAdminDateFormat() . " | " . $transaction->transactionId . " | " . $transaction->amountIn . "\n</option>";
    }
    echo "                </select>\n            </td>\n        </tr>\n        <tr>\n            <td class=\"fieldlabel\">";
    echo AdminLang::trans("fields.amount");
    echo "</td>\n            <td class=\"fieldarea\">\n                <div class=\"input-group input-300\">\n                    <input type=\"text\"\n                           name=\"amount\"\n                           id=\"amount\"\n                           class=\"form-control\"\n                           placeholder=\"0.00\">\n                    <span class=\"input-group-addon\">Leave blank for full refund</span>\n                </div>\n            </td>\n        </tr>\n        <tr>\n            <td class=\"fieldlabel\">";
    echo AdminLang::trans("invoices.refundtype");
    echo "</td>\n            <td class=\"fieldarea\">\n                <select name=\"refund_type\"\n                        id=\"refundType\"\n                        class=\"form-control select-inline\"\n                        onchange=\"showRefundTransactionId();return false\"\n                >\n                    <option value=\"sendtogateway\">\n                        ";
    echo AdminLang::trans("invoices.refundtypegateway");
    echo "                    </option>\n                    <option value=\"\" type=\"\">\n                        ";
    echo AdminLang::trans("invoices.refundtypemanual");
    echo "                    </option>\n                    <option value=\"addascredit\">\n                        ";
    echo AdminLang::trans("invoices.refundtypecredit");
    echo "                    </option>\n                </select>\n            </td>\n        </tr>\n        <tr id=\"refundTransactionId\" style=\"display:none;\">\n            <td class=\"fieldlabel\">";
    echo AdminLang::trans("fields.transid");
    echo "</td>\n            <td class=\"fieldarea\">\n                <input type=\"text\" name=\"refund_transaction_id\" size=\"25\" class=\"form-control\">\n            </td>\n        </tr>\n        <tr>\n            <td class=\"fieldlabel\">\n                ";
    echo AdminLang::trans("invoices.reverse");
    echo "            </td>\n            <td class=\"fieldarea\">\n                <label class=\"checkbox-inline\">\n                    <input type=\"hidden\" name=\"reverse\" value=\"0\">\n                    <input type=\"checkbox\" name=\"reverse\" value=\"1\">\n                    ";
    echo AdminLang::trans("invoices.reverseDescription");
    echo "                </label>\n            </td>\n        </tr>\n        <tr>\n            <td class=\"fieldlabel\">\n                ";
    echo AdminLang::trans("global.sendemail");
    echo "            </td>\n            <td class=\"fieldarea\">\n                <label class=\"checkbox-inline\">\n                    <input type=\"checkbox\" name=\"sendemail\" checked=\"checked\">\n                    ";
    echo AdminLang::trans("invoices.ticksendconfirmation");
    echo "                </label>\n            </td>\n        </tr>\n        ";
    if(0 < $invoiceCredit) {
        echo "            <tbody id=\"creditArea\">\n            <tr>\n                <td class=\"fieldlabel\"></td>\n                <td class=\"fieldarea\">\n                    <div class=\"alert alert-warning no-margin\">\n                        ";
        echo $refundWarning;
        echo "<br>\n                        ";
        echo $refundLabelText;
        echo "                    </div>\n                </td>\n            </tr>\n            ";
        if($refundCheckboxText) {
            echo "                <tr>\n                    <td class=\"fieldlabel\"></td>\n                    <td class=\"fieldarea\">\n                        <label class=\"checkbox-inline\">\n                            <input type=\"checkbox\" id=\"warning\" name=\"warning\" value=\"leaveCredit\" onclick=\"selectRefundChoice(this);\">\n                            ";
            echo $refundCheckboxText;
            echo "                        </label>\n                    </td>\n                </tr>\n            ";
        } else {
            echo "                ";
            foreach ($refundRadioOptions as $key => $value) {
                echo "                    <tr>\n                        <td class=\"fieldlabel\"></td>\n                        <td class=\"fieldarea\">\n                            <label class=\"checkbox-inline\">\n                                <input type=\"checkbox\"\n                                       id=\"warning_";
                echo $key;
                echo "\"\n                                       name=\"warning\"\n                                       value=\"";
                echo $key;
                echo "\"\n                                       onclick=\"selectRefundChoice(this);\"\n                                >\n                                ";
                echo $value;
                echo "                            </label>\n                        </td>\n                    </tr>\n                ";
            }
            echo "            ";
        }
        echo "            <input type=\"hidden\" name=\"invoice_credit\" id=\"invoiceCredit\" value=\"";
        echo $invoiceCredit;
        echo "\">\n            </tbody>\n        ";
    }
    echo "    </table>\n    <div class=\"btn-container\">\n        <button type=\"submit\"\n                class=\"btn btn-default\"\n                id=\"refundBtn\"\n            ";
    echo !valueIsZero($invoiceCredit) ? " disabled=\"disabled\"" : "";
    echo "        >\n            ";
    echo AdminLang::trans("invoices.refund");
    echo "        </button>\n    </div>\n</form>\n";
} elseif(!$refundPermission) {
    echo WHMCS\View\Helper::alert("You do not have permission to refund invoice payments.");
} else {
    echo WHMCS\View\Helper::alert("There are no transactions that are eligible for a refund.");
}

?>