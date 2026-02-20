<?php

echo $aInt->nextAdminTab();
if($addPaymentPermission) {
    if(0 < $invoice->total && $invoice->balance <= 0) {
        echo WHMCS\View\Helper::alert(AdminLang::trans("invoices.paidstatuscredit") . "<br>" . AdminLang::trans("invoices.paidstatuscreditdesc"), "warning");
    }
    echo "\n    <form method=\"post\"\n          id=\"addPayment\"\n          action=\"";
    echo routePath("admin-billing-view-invoice-add-payment", $invoice->id);
    echo "\"\n    >\n        <input type=\"hidden\" name=\"view\" value=\"1\">\n        <div class=\"table-responsive\">\n            <table class=\"form\" width=\"100%\" border=\"0\" cellspacing=\"2\" cellpadding=\"3\">\n                <tr>\n                    <td width=\"20%\" class=\"fieldlabel\">\n                        ";
    echo AdminLang::trans("fields.date");
    echo "                    </td>\n                    <td class=\"fieldarea\">\n                        <div class=\"form-group date-picker-prepend-icon\">\n                            <label for=\"inputDate\" class=\"field-icon\">\n                                <i class=\"fal fa-calendar-alt\"></i>\n                            </label>\n                            <input id=\"inputDate\"\n                                   type=\"text\"\n                                   name=\"date\"\n                                   value=\"";
    echo $addPaymentDate;
    echo "\"\n                                   class=\"form-control date-picker-single\"\n                            />\n                        </div>\n                    </td>\n                    <td width=\"20%\" class=\"fieldlabel\">\n                        ";
    echo AdminLang::trans("fields.amount");
    echo "                    </td>\n                    <td class=\"fieldarea\">\n                        <input type=\"text\"\n                               name=\"amount\"\n                               value=\"";
    echo $addPaymentBalance;
    echo "\"\n                               class=\"form-control input-150\"\n                        >\n                    </td>\n                </tr>\n                <tr>\n                    <td class=\"fieldlabel\">\n                        ";
    echo AdminLang::trans("fields.paymentmethod");
    echo "                    </td>\n                    <td class=\"fieldarea\">\n                        ";
    echo $paymentMethodDropDown;
    echo "                    </td>\n                    <td class=\"fieldlabel\">\n                        ";
    echo AdminLang::trans("fields.fees");
    echo "                    </td>\n                    <td class=\"fieldarea\">\n                        <input type=\"text\"\n                               name=\"fees\"\n                               value=\"";
    echo $addPaymentFees;
    echo "\"\n                               class=\"form-control input-150\"\n                        >\n                    </td>\n                </tr>\n                <tr>\n                    <td class=\"fieldlabel\">\n                        ";
    echo AdminLang::trans("fields.transid");
    echo "                    </td>\n                    <td class=\"fieldarea\">\n                        <input type=\"text\"\n                               name=\"transid\"\n                               value=\"";
    echo $addPaymentTransactionId;
    echo "\"\n                               class=\"form-control input-250\"\n                        >\n                    </td>\n                    <td class=\"fieldlabel\">\n                        ";
    echo AdminLang::trans("global.sendemail");
    echo "                    </td>\n                    <td class=\"fieldarea\">\n                        <label class=\"checkbox-inline\">\n                            <input type=\"hidden\" name=\"sendconfirmation\" value=\"0\">\n                            <input type=\"checkbox\"\n                                   name=\"sendconfirmation\"\n                                ";
    echo $addPaymentSendConfirmationChecked;
    echo "                            >\n                            ";
    echo AdminLang::trans("invoices.ticksendconfirmation");
    echo "                        </label>\n                    </td>\n                </tr>\n            </table>\n        </div>\n        <div class=\"btn-container\">\n            <button id=\"btnAddPayment\" type=\"submit\" class=\"btn btn-primary\">\n            <span id=\"paymentText\">\n                ";
    echo AdminLang::trans("invoices.addpayment");
    echo "            </span>\n                <span id=\"paymentLoading\" class=\"hidden\">\n                <i class=\"fas fa-spinner fa-spin\"></i> ";
    echo AdminLang::trans("global.loading");
    echo "            </span>\n            </button>\n        </div>\n    </form>\n    ";
} else {
    echo WHMCS\View\Helper::alert("You do not have the appropriate permission to add a payment.");
}

?>