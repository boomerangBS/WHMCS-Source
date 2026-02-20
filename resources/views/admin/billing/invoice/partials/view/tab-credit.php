<?php

echo $aInt->nextAdminTab();
echo "<div class=\"row text-center\">\n    <div class=\"col-md-offset-2 col-md-4 col-sm-12\">\n        <b>";
echo AdminLang::trans("invoices.addcredit");
echo "</b>\n        <form method=\"post\" action=\"";
echo routePath("admin-billing-invoice-add-credit", $invoice->id);
echo "\">\n            ";
echo generate_token();
echo "            <input type=\"hidden\" name=\"view\" value=\"1\">\n            <input type=\"number\"\n                   min=\"0\"\n                   step=\"";
echo $currencyStep;
echo "\"\n                   name=\"addcredit\"\n                   value=\"";
echo $invoice->balance <= $clientCredit ? $invoice->balance : $clientCredit;
echo "\"\n                   class=\"form-control input-100 input-inline\"\n                   ";
echo $clientCredit == "0.00" ? " disabled" : "";
echo "            >\n            <button type=\"submit\"\n                    class=\"btn btn-default";
echo valueIsZero($clientCredit) ? " disabled" : "";
echo "\"\n                ";
echo valueIsZero($clientCredit) ? " disabled" : "";
echo "            >\n                ";
echo AdminLang::trans("global.go");
echo "            </button>\n        </form>\n        <span style=\"color: #377D0D;\">\n            ";
echo formatCurrency($clientCredit, $invoice->client->currencyId);
echo "            ";
echo AdminLang::trans("invoices.creditavailable");
echo "        </span>\n    </div>\n    <div class=\"col-md-4 col-sm-12\">\n        <b>";
echo AdminLang::trans("invoices.removecredit");
echo "</b>\n        <form method=\"post\" action=\"";
echo routePath("admin-billing-invoice-remove-credit", $invoice->id);
echo "\">\n            ";
echo generate_token();
echo "            <input type=\"hidden\" name=\"view\" value=\"1\">\n            <input type=\"number\"\n                   min=\"0\"\n                   step=\"";
echo $currencyStep;
echo "\"\n                   name=\"removecredit\"\n                   value=\"0.00\"\n                   class=\"form-control input-100 input-inline\"\n                ";
echo valueIsZero($invoice->credit) ? " disabled" : "";
echo "            >\n            <button type=\"submit\"\n                   class=\"btn btn-default";
echo valueIsZero($invoice->credit) ? " disabled" : "";
echo "\"\n                ";
echo valueIsZero($invoice->credit) ? " disabled" : "";
echo "            >\n                ";
echo AdminLang::trans("global.go");
echo "            </button>\n        </form>\n        <span style=\"color: #cc0000;\">\n            ";
echo formatCurrency($invoice->credit, $invoice->client->currencyId);
echo "            ";
echo AdminLang::trans("invoices.creditavailable");
echo "        </span>\n    </div>\n</div>\n</form>\n";

?>