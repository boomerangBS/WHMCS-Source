<?php

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