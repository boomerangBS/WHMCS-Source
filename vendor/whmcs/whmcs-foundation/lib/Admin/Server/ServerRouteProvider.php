<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Admin\Server;

class ServerRouteProvider implements \WHMCS\Route\Contracts\DeferredProviderInterface
{
    use \WHMCS\Route\AdminProviderTrait;
    public function getRoutes()
    {
        $routes = ["/admin/setup/servers" => ["attributes" => ["authentication" => "admin"], ["method" => ["POST"], "name" => $this->getDeferredRoutePathNameAttribute() . "meta-refresh", "path" => "/meta/refresh", "handle" => ["WHMCS\\Admin\\Server\\ServerController", "refreshRemoteData"], "authorization" => function () {
            return (new \WHMCS\Admin\ApplicationSupport\Route\Middleware\Authorization())->setRequireAllPermission(["Configure Servers"]);
        }]]];
        return $routes;
    }
    public function getDeferredRoutePathNameAttribute()
    {
        return "admin-setup-servers-";
    }
}

?>