<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
echo "<form name=\"frmApiCredentialManage\" action=\"";
echo routePath("admin-setup-authz-api-devices-update");
echo "\">\n    <input type=\"hidden\" name=\"token\" value=\"";
echo $csrfToken;
echo "\">\n    <input type=\"hidden\" name=\"id\" value=\"";
echo $device->id;
echo "\">\n    ";
echo $this->insert("partials/attributes-api-credentials");
echo "</form>\n";

?>