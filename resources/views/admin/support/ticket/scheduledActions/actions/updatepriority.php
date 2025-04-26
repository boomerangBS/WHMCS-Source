<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
$actionContainer->layout($this);
$ticketPriorityOptions = collect($ticketPriorities)->mapWithKeys(function ($identifier) {
    $key = "status." . $identifier;
    $label = AdminLang::trans($key);
    if($label === $key) {
        $label = $identifier;
    }
    return [$identifier => $label];
});
echo "<div>\n    <select name=\"parameters[";
echo $actionName;
echo "][priority]\"\n            class=\"form-control scheduled-actions-dropdown scheduled-actions-parameter\"\n            disabled=\"disabled\">\n        ";
echo $this->selectOptions($ticketPriorityOptions, $selectedPriority);
echo "    </select>\n</div>\n";

?>