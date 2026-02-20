<?php

define("ADMINAREA", true);
require dirname(__DIR__) . DIRECTORY_SEPARATOR . "init.php";
$aInt = new WHMCS\Admin("loginonly");
$aInt->title = $aInt->lang("permissions", "accessdenied");
$aInt->sidebar = "home";
$aInt->icon = "warning";
$whmcs = App::self();
$permid = $whmcs->get_req_var("permid");
$adminPermissions = getAdminPermsArray();
$requestedPermission = empty($adminPermissions[$permid]) ? "Unknown" : $adminPermissions[$permid];
logActivity("Access Denied to " . $requestedPermission);
$displayPermission = AdminLang::trans("permissions." . $permid);
if($displayPermission == "permissions." . $permid) {
    $displayPermission = $requestedPermission;
}
$accessDenied = AdminLang::trans("permissions.accessdenied");
$noPermission = AdminLang::trans("permissions.nopermission");
$action = AdminLang::trans("permissions.action");
$goBack = AdminLang::trans("global.goback");
$aInt->content = "<div class=\"error-page\">\n    <div class=\"error-heading\">\n        <h3 class=\"error-title\">\n            <i class=\"fas fa-exclamation-triangle\"></i>\n            " . $accessDenied . "\n        </h3>\n    </div>\n    <div class=\"error-body\">\n        <p>" . $noPermission . "</p>\n        <p>\n            <strong>" . $action . "</strong><br />\n            <span id=\"missingPermission\">" . $displayPermission . "</span>\n            \n        </p>\n    </div>\n    <div class=\"error-footer\">\n        <button type=\"button\" class=\"btn btn-default btn-lg\" onclick=\"history.go(-1)\">\n            <i class=\"fas fa-arrow-circle-left\"></i>\n            " . $goBack . "\n        </button>\n    </div>\n</div>";
$updater = new WHMCS\Installer\Update\Updater();
$aInt->templatevars["licenseinfo"] = ["registeredname" => $licensing->getRegisteredName(), "productname" => $licensing->getProductName(), "expires" => $licensing->getExpiryDate(), "currentversion" => $whmcs->getVersion()->getCasual(), "latestversion" => $updater->getLatestVersion()->getCasual(), "updateavailable" => $updater->isUpdateAvailable()];
$aInt->display();

?>