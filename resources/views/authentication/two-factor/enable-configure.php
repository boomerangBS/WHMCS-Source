<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
echo "<form action=\"";
echo routePath(($isAdmin ? "admin-" : "") . "account-security-two-factor-enable-verify");
echo "\" onsubmit=\"dialogSubmit();return false\">\n    ";
echo generate_token("form");
echo "    <input type=\"hidden\" name=\"step\" value=\"verify\" />\n    <input type=\"hidden\" name=\"module\" value=\"";
echo $module;
echo "\" />\n    ";
echo $twoFactorConfigurationOutput;
echo "</form>\n";

?>