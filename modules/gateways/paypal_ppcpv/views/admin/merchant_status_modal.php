<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
$yes = "<i class=\"far fa-check-circle fa-lg\"></i>";
$no = "<i class=\"far fa-exclamation-triangle fa-lg\"></i>";
$envHuman = function ($env) {
    return AdminLang::trans(sprintf("paypalCommerce.labelEnvironment%s", ucfirst($env->label)));
};
echo "\n<table class=\"table table-striped\">\n<thead>\n    <tr>\n        <th>";
echo AdminLang::trans("paypalCommerce.labelEnvironment");
echo "</th>\n        ";
foreach ($environmentStatuses as $env) {
    echo "        <td>\n            ";
    echo $envHuman($env);
    echo "        </td>\n        ";
}
echo "    </tr>\n</thead>\n<tbody>\n<tr>\n    <th>";
echo AdminLang::trans("paypalCommerce.labelPaymentsReceivable");
echo "</th>\n    ";
foreach ($environmentStatuses as $env) {
    $status = $environmentStatuses[$env];
    echo "        <td>";
    echo $status->paymentsReceivable() ? $yes : $no;
    echo "</td>\n    ";
}
echo "</tr>\n<tr>\n    <th>";
echo AdminLang::trans("paypalCommerce.labelEmailVerified");
echo "</th>\n    ";
foreach ($environmentStatuses as $env) {
    $status = $environmentStatuses[$env];
    echo "        <td>";
    echo $status->emailVerified() ? $yes : $no;
    echo "</td>\n    ";
}
echo "</tr>\n<tr>\n    <th>";
echo AdminLang::trans("paypalCommerce.capabilityVaultLabel");
echo "</th>\n    ";
foreach ($environmentStatuses as $env) {
    $status = $environmentStatuses[$env];
    echo "        <td>";
    echo $status->vaultCapable() ? $yes : $no;
    echo "</td>\n    ";
}
echo "</tr>\n<tr>\n    <th>";
echo AdminLang::trans("paypalCommerce.capabilityCardsLabel");
echo "</th>\n    ";
foreach ($environmentStatuses as $env) {
    $status = $environmentStatuses[$env];
    echo "        <td>";
    echo $status->cardsCapable() ? $yes : $no;
    echo "</td>\n    ";
}
echo "</tr>\n</tbody>\n</table>\n<script type=\"application/javascript\">\njQuery(document).ready(() => {\n    let clickNamespace = '";
echo $module;
echo "_merchant_status';\n    jQuery('#modalAjaxClose').on('click.' + clickNamespace, () => {\n        jQuery('#modalAjaxClose').off('click.' + clickNamespace);\n        let url = new URL(document.location);\n        let query = url.searchParams;\n        query.set('manage', '";
echo $module;
echo "');\n        window.location.replace(\n            window.location.href\n                .replace(window.location.search, '')\n                .replace(window.location.hash, '')\n            + '?'\n            + query.toString()\n            + url.hash\n        );\n    })\n});\n</script>\n";

?>