<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
$this->layout("layouts/admin-content");
$this->start("body");
echo "\n<div class=\"redirect-msg\">\nYou are now being redirected to the WHMCS Marketplace.<br>\nWhen you are finished, simply close this tab to return to WHMCS.<br><br>\n<small>If you are not automatically redirected within 5 seconds, please <a href=\"#\">click here</a></small>\n</div>\n\n<script>\n\$(document).ready(function() {\n    WHMCS.http.jqClient.post('', 'action=doSsoRedirect&destination=";
echo $ssoDestination;
echo "', function(data) {\n        window.location = data.redirectUrl;\n    }, 'json');\n});\n</script>\n";
$this->end();

?>