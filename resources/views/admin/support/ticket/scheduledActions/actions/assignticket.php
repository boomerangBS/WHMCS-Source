<?php

$actionContainer->layout($this);
echo "<div>\n    <select name=\"parameters[";
echo $actionName;
echo "][assignAdminId]\"\n            class=\"form-control scheduled-actions-dropdown scheduled-actions-parameter\"\n            disabled=\"disabled\">\n        ";
echo $this->selectOptions($assignableAdmins, $selectedAdmin);
echo "    </select>\n</div>\n";

?>