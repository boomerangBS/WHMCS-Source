<?php

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