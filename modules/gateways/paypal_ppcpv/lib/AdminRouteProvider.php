<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

namespace WHMCS\Module\Gateway\paypal_ppcpv;

// Decoded file for php version 72.
class AdminRouteProvider implements \WHMCS\Route\Contracts\DeferredProviderInterface
{
    use \WHMCS\Route\AdminProviderTrait;
    public function getRoutes()
    {
        return ["/admin/paypal_ppcpv" => [["method" => ["GET", "POST"], "name" => $this->getDeferredRoutePathNameAttribute() . "unlink", "path" => "/unlink", "handle" => ["WHMCS\\Module\\Gateway\\paypal_ppcpv\\RouteController", "unlink"]]]];
    }
    public function getDeferredRoutePathNameAttribute()
    {
        return "admin-paypal_ppcpv-";
    }
}

?>