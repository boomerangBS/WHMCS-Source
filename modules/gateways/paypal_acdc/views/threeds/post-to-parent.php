<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
echo "\n<html>\n    <head>\n        <title>";
echo WHMCS\Config\Setting::getValue("CompanyName");
echo "</title>\n    </head>\n    <body onload=\"document.frmThreeDSResultPage.submit();\">\n        <form name=\"frmThreeDSResultPage\" method=\"post\" action=\"";
echo $redirectPage;
echo "\" target=\"_parent\">\n            <input type=\"hidden\" name=\"3dsc\" value=\"";
echo $challenge;
echo "\">\n            <noscript>\n                <br>\n                <br>\n                <center>\n                    <p style=\"color:#cc0000;\"><b>Processing Your Transaction</b></p>\n                    <p>JavaScript is currently disabled or is not supported by your browser.</p>\n                    <p>Please click Submit to continue the processing of your transaction.</p>\n                    <input type=\"submit\" value=\"Submit\">\n                </center>\n            </noscript>\n        </form>\n    </body>\n</html>\n";

?>