<?php

echo "<div class=\"form-group\">\n    <label>";
echo AdminLang::trans("global.gateway");
echo "</label>\n    <br>\n    <span>";
echo WHMCS\Input\Sanitize::encodeToCompatHTML($moduleDisplayName);
echo "</span>\n</div>\n<div class=\"row\">\n    <div class=\"col-sm-12\">\n        <div class=\"form-group\">\n            <label for=\"inputDescription\">";
echo AdminLang::trans("global.description");
echo "</label>\n            <input type=\"text\"\n                id=\"inputDescription\"\n                name=\"description\"\n                value=\"";
echo $payMethod->description;
echo "\"\n                class=\"form-control\"\n                readonly />\n        </div>\n    </div>\n</div>\n<div class=\"row\">\n    <div class=\"col-sm-12\">\n        <div class=\"form-group\">\n            <label for=\"inputGatewayToken\">";
echo AdminLang::trans("payments.gatewayToken");
echo "</label>\n            <input class=\"form-control\"\n                id=\"inputGatewayToken\"\n                type=\"text\"\n                value=\"";
echo WHMCS\Input\Sanitize::encodeToCompatHTML($payMethod->payment->getRemoteToken());
echo "\"\n                readonly />\n        </div>\n    </div>\n</div>\n";

?>