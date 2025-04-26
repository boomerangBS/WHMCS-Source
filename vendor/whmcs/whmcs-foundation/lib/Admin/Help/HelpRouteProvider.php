<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Admin\Help;

class HelpRouteProvider implements \WHMCS\Route\Contracts\DeferredProviderInterface
{
    use \WHMCS\Route\AdminProviderTrait;
    public function getRoutes()
    {
        $helpRoutes = ["/admin/help" => ["attributes" => ["authentication" => "admin", "authorization" => function () {
            return (new \WHMCS\Admin\ApplicationSupport\Route\Middleware\Authorization())->setRequireAllPermission(["Main Homepage"]);
        }], ["method" => ["GET"], "name" => "admin-help-license", "path" => "/license", "handle" => ["\\WHMCS\\Admin\\Help\\HelpController", "viewLicense"]], ["method" => ["POST"], "name" => "admin-help-license-check", "path" => "/license/check", "handle" => ["\\WHMCS\\Admin\\Help\\HelpController", "forceLicenseCheck"], "authorization" => function (\WHMCS\Admin\ApplicationSupport\Route\Middleware\Authorization $authz) {
            return $authz->requireCsrfToken();
        }], ["method" => ["POST"], "name" => "admin-help-license-upgrade-data", "path" => "/license/upgrade/data", "handle" => ["\\WHMCS\\Admin\\Help\\HelpController", "fetchLicenseUpgradeData"], "authorization" => function (\WHMCS\Admin\ApplicationSupport\Route\Middleware\Authorization $authz) {
            return $authz->requireCsrfToken();
        }], ["method" => ["POST"], "name" => "admin-help-license-upgrade-send", "path" => "/license/upgrade/send", "handle" => ["\\WHMCS\\Admin\\Help\\HelpController", "sendLicenseUpgradeRequest"], "authorization" => function (\WHMCS\Admin\ApplicationSupport\Route\Middleware\Authorization $authz) {
            return $authz->requireCsrfToken();
        }]]];
        return $helpRoutes;
    }
    public function getDeferredRoutePathNameAttribute()
    {
        return "admin-help-";
    }
}

?>