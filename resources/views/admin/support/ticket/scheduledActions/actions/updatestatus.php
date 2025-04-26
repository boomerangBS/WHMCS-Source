<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
$actionContainer->layout($this);
$options = collect($ticketStatuses)->mapWithKeys(function (WHMCS\Support\Ticket\Status $status) {
    return [$status->id => $status->adminTitle()];
});
$selected = isset($selectedStatus) ? WHMCS\Support\Ticket\Status::translateTitleForAdmin($selectedStatus) : "";
echo "<div>\n    <select name=\"parameters[";
echo $actionName;
echo "][status]\"\n            class=\"form-control scheduled-actions-dropdown scheduled-actions-parameter\"\n            disabled=\"disabled\">\n        ";
echo $this->selectOptions($options, $selected);
echo "    </select>\n</div>\n";

?>