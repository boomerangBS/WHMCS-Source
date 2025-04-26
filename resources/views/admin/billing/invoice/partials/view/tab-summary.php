<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
echo "<div class=\"row\">\n    <div class=\"col-md-6 col-sm-12\">\n        <table class=\"form\" width=\"100%\">\n            <tr>\n                <td width=\"35%\" class=\"fieldlabel\">\n                    ";
echo AdminLang::trans("fields.clientname");
echo "                </td>\n                <td class=\"fieldarea\">\n                    ";
echo $aInt->outputClientLink($clientId);
echo "                    (<a href=\"";
echo $webroot;
echo "/clientsinvoices.php?userid=";
echo $clientId;
echo "\">\n                        ";
echo AdminLang::trans("invoices.viewinvoices");
echo "                    </a>)\n                </td>\n            </tr>\n            ";
if($invoice->invoiceNumber) {
    echo "                <tr>\n                    <td class=\"fieldlabel\">\n                        ";
    echo AdminLang::trans("fields.invoicenum");
    echo "                    </td>\n                    <td class=\"fieldarea\">\n                        ";
    echo $invoice->invoiceNumber;
    echo "                    </td>\n                </tr>\n            ";
}
echo "            <tr>\n                <td class=\"fieldlabel\">\n                    ";
echo AdminLang::trans("fields.invoicedate");
echo "                </td>\n                <td class=\"fieldarea\">\n                    ";
echo $invoice->dateCreated->toAdminDateFormat();
echo "                </td>\n            </tr>\n            <tr>\n                <td class=\"fieldlabel\">\n                    ";
echo AdminLang::trans("fields.duedate");
echo "                </td><td class=\"fieldarea\">";
echo $invoice->dateDue->toAdminDateFormat();
echo "</td>\n            </tr>\n            <tr>\n                <td class=\"fieldlabel\">\n                    ";
echo AdminLang::trans("fields.invoiceamount");
echo "                </td><td class=\"fieldarea\">\n                    ";
echo formatCurrency($invoice->total + $invoice->credit, $invoice->client->currencyId);
echo "                </td>\n            </tr>\n            <tr>\n                <td class=\"fieldlabel\">\n                    ";
echo AdminLang::trans("fields.balance");
echo "                </td>\n                <td class=\"fieldarea\">\n                <span id=\"invoiceBalance\" style=\"font-weight: bold; color: ";
echo 0 < $invoice->balance ? "#cc0000" : "#99cc00";
echo ";\">\n                    ";
echo formatCurrency($invoice->balance, $invoice->client->currencyId);
echo "                </span>\n                </td>\n            </tr>\n        </table>\n    </div>\n    <div class=\"col-md-6 col-sm-12 text-center\">\n        <span class=\"invoice-status ";
echo $statusClass;
echo "\">\n            ";
echo AdminLang::trans("status." . strtolower(str_replace(" ", "", $invoice->status)));
echo "        </span>\n        ";
echo $lastCaptureAttempt;
echo "        <br>\n        ";
echo AdminLang::trans("fields.paymentmethod");
echo ":\n        ";
if(in_array($invoice->status, [$invoice::STATUS_UNPAID, $invoice::STATUS_DRAFT])) {
    echo "            <select name=\"payment_gateway\" id=\"selectPaymentGateway\" class=\"form-control select-inline\">\n                ";
    foreach ($availablePaymentGateways as $gatewayName => $friendlyName) {
        $selected = "";
        if($gatewayName === $invoice->paymentGateway) {
            $selected = " selected=\"selected\"";
        }
        echo "<option value=\"" . $gatewayName . "\"" . $selected . ">" . $friendlyName . "</option>";
    }
    echo "            </select>\n            <div id=\"gatewayLoading\" class=\"inline loadingspinner\" style=\"display: none;\">\n                <span><i class=\"fas fa-spinner fa-spin\"></i></span>\n            </div>\n        ";
} else {
    echo "        <strong>";
    echo $invoice->paymentGatewayName;
    echo "</strong>\n        ";
}
echo "        ";
echo $payMethodOutput;
echo "        <form method=\"post\"\n              action=\"";
echo routePath("admin-billing-invoice-email-send", $invoice->id);
echo "\"\n              class=\"top-margin-10 bottom-margin-5\"\n        >\n            <select name=\"template\" class=\"form-control select-inline\">\n                ";
foreach ($emailTemplates as $emailTemplate) {
    echo "                    <option>";
    echo $emailTemplate;
    echo "</option>\n                ";
}
echo "            </select>\n            <button type=\"submit\"\n                    id=\"btnSendEmail\"\n                    class=\"btn btn-default";
echo $sendEmailDisabled ? " disabled" : "";
echo "\"\n                ";
echo $sendEmailDisabled ? "disabled=\"disabled\"" : "";
echo "            >\n                ";
echo AdminLang::trans("global.sendemail");
echo "            </button>\n        </form>\n        <a href=\"";
echo routePath("admin-client-view-invoice-capture", $clientId, $invoice->id);
echo "\"\n           class=\"btn btn-success open-modal\"";
echo $captureDisabled;
echo "           id=\"btnShowAttemptCaptureDialog\"\n           data-btn-submit-id=\"btnAttemptCapture\"\n           data-btn-submit-label=\"";
echo $captureButtonText;
echo "\"\n           data-modal-title=\"";
echo $captureButtonText;
echo "\"\n        >\n            ";
echo $captureButtonText;
echo "        </a>\n    </div>\n</div>\n";

?>