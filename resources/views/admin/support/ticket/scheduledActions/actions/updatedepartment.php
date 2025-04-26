<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
$actionContainer->layout($this);
$availableDepartmentOptions = collect($departments)->mapWithKeys(function (WHMCS\Support\Department $department) {
    return [$department->id => $department->name];
});
echo "<div>\n    <select name=\"parameters[";
echo $actionName;
echo "][department]\"\n            class=\"form-control scheduled-actions-dropdown scheduled-actions-parameter\"\n            disabled=\"disabled\">\n        ";
echo $this->selectOptions($availableDepartmentOptions, $selectedDepartment);
echo "    </select>\n</div>\n";

?>