<?php

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