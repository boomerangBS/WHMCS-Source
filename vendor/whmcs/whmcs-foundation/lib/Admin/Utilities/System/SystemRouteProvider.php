<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Admin\Utilities\System;

class SystemRouteProvider implements \WHMCS\Route\Contracts\DeferredProviderInterface
{
    use \WHMCS\Route\AdminProviderTrait;
    public function getRoutes()
    {
        $routes = ["/admin/utilities/system" => ["attributes" => ["authentication" => "admin"], ["method" => ["GET"], "name" => "admin-utilities-system-phpcompat", "path" => "/php-compat", "handle" => ["WHMCS\\Admin\\Utilities\\System\\PhpCompat\\PhpCompatController", "index"], "authorization" => function () {
            return (new \WHMCS\Admin\ApplicationSupport\Route\Middleware\Authorization())->setRequireAllPermission(["View PHP Info"]);
        }], ["method" => ["POST"], "name" => "admin-utilities-system-phpcompat-scan", "path" => "/php-compat/scan", "handle" => ["WHMCS\\Admin\\Utilities\\System\\PhpCompat\\PhpCompatController", "scan"], "authorization" => function () {
            return (new \WHMCS\Admin\ApplicationSupport\Route\Middleware\Authorization())->setRequireAllPermission(["View PHP Info"])->requireCsrfToken();
        }], ["method" => ["GET", "POST"], "name" => "admin-utilities-system-automation-data", "path" => "/automation/detail/{namespaceId:\\d+}/{date}[/tab{tab:\\d+}]", "handle" => ["WHMCS\\Admin\\Utilities\\System\\Automation\\AutomationController", "getDetail"], "authorization" => function () {
            return (new \WHMCS\Admin\ApplicationSupport\Route\Middleware\Authorization())->setRequireAllPermission(["Automation Status"]);
        }]]];
        return $routes;
    }
    public function getDeferredRoutePathNameAttribute()
    {
        return "admin-utilities-system-";
    }
}

?>