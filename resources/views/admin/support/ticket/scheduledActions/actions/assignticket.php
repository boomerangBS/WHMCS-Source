<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
$actionContainer->layout($this);
echo "<div>\n    <select name=\"parameters[";
echo $actionName;
echo "][assignAdminId]\"\n            class=\"form-control scheduled-actions-dropdown scheduled-actions-parameter\"\n            disabled=\"disabled\">\n        ";
echo $this->selectOptions($assignableAdmins, $selectedAdmin);
echo "    </select>\n</div>\n";

?>