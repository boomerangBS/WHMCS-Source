<?php

Hook::add("AdminPredefinedAddons", -100, function () {
    $newText = AdminLang::trans("global.new");
    $sitejetTitle = AdminLang::trans("addons.predefinedAddons.sitejet.title");
    $sitejetPanelDescription = AdminLang::trans("addons.predefinedAddons.sitejet.panelDescription", [":panel" => "cPanel"]);
    $sitejetAddonDescription = AdminLang::trans("addons.predefinedAddons.sitejet.defaultDescription");
    return [["module" => "cpanel", "icontype" => "url", "iconvalue" => "./images/sitejet/offer-sitejet-as-an-addon.png", "labeltype" => "success", "labelvalue" => $newText . "!", "paneltitle" => $sitejetTitle . " (cPanel)", "paneldescription" => $sitejetPanelDescription, "addonname" => $sitejetTitle, "addondescription" => $sitejetAddonDescription, "welcomeemail" => "Sitejet Builder Welcome Email", "featureaddon" => "sitejet", "tag" => "sitejet"], ["module" => "cpanel", "icontype" => "fa", "iconvalue" => "fad fa-cube", "paneltitle" => "WP Toolkit Deluxe (cPanel)", "paneldescription" => "Automate provisioning of WP Toolkit Deluxe for cPanel Hosting Accounts", "addonname" => "WP Toolkit Deluxe", "addondescription" => "WP Toolkit Deluxe gives you advanced features like plugin and theme management, staging, cloning, and Smart Updates!", "welcomeemail" => "WP Toolkit Welcome Email", "featureaddon" => "wp-toolkit-deluxe"]];
});
add_hook("AfterModuleTerminate", 1, function ($vars) {
    $model = $vars["params"]["model"];
    if(is_null($model->product) || $model->product->module !== "cpanel") {
        return NULL;
    }
    $serviceWordPressInstances = $model->serviceProperties->get("WordPress Instances");
    if($serviceWordPressInstances) {
        logActivity("Deleting WordPress instances on service termination: " . $serviceWordPressInstances . ", " . $model instanceof WHMCS\Service\Service ? "Service" : "Addon ID: " . $model->id);
        $model->serviceProperties->save(["WordPress Instances" => WHMCS\Input\Sanitize::encode(json_encode([]))]);
    }
});

?>