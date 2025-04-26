<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
define("ADMINAREA", true);
require "../init.php";
$aInt = new WHMCS\Admin("View PHP Info");
$aInt->title = $aInt->lang("system", "phpinfo");
$aInt->sidebar = "utilities";
$aInt->icon = "phpinfo";
$aInt->requireAuthConfirmation();
$aInt->content = "<p>The phpinfo() function is unavailable.</p>";
$phpInfo = WHMCS\Environment\Php::info();
if(!is_null($phpInfo)) {
    $phpInfo = preg_replace("%^.*<body>(.*)</body>.*\$%ms", "\$1", $phpInfo);
    $aInt->content = "<div class=\"whmcs-phpinfo\">" . $phpInfo . "</div>";
}
$aInt->display();

?>