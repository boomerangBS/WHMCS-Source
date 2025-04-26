<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
echo "<div class=\"form-group\">\n    <label for=\"inputDescription\">";
echo AdminLang::trans("global.description");
echo "</label>\n    <input type=\"text\" class=\"form-control\" id=\"inputDescription\" name=\"description\"\n       placeholder=\"";
echo AdminLang::trans("global.description");
echo "\"\n       value=\"";
echo isset($device) ? $device->description : "";
echo "\"\n    >\n</div>\n<div class=\"form-group\">\n    <label for=\"selectRoles\">";
echo AdminLang::trans("apicreds.apiRoles");
echo "</label>\n    <select multiple class=\"form-control\" id=\"selectRoles\" name=\"roleIds[]\">\n        ";
if(!empty($roles)) {
    if(isset($device)) {
        $currentRoles = $device->rolesCollection();
    } else {
        $currentRoles = [];
    }
    foreach ($roles as $role) {
        echo sprintf("<option value=\"%s\" %s>%s</option>", $role->id, array_key_exists($role->id, $currentRoles) ? "selected" : "", $role->role);
    }
} else {
    echo sprintf("<option value=\"\" disabled>%s</option>", AdminLang::trans("apirole.noRolesDefined"));
}
echo "    </select>\n    <p class=\"help-block\">\n        ";
echo AdminLang::trans("apicreds.roleSelectionHelper");
echo "    </p>\n</div>\n";

?>