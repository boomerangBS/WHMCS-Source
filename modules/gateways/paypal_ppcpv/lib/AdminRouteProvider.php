<?php


namespace WHMCS\Module\Gateway\paypal_ppcpv;
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