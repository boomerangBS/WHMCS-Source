<?php

namespace WHMCS\Promotions;

class RouteProvider implements \WHMCS\Route\Contracts\DeferredProviderInterface
{
    use \WHMCS\Route\AdminProviderTrait;
    public function getRoutes() : array
    {
        return ["/admin/promotions" => [["method" => ["GET", "POST"], "name" => $this->getDeferredRoutePathNameAttribute() . "dismiss", "path" => "/dismiss/{identifier}", "authentication" => "admin", "authorization" => function () {
            return (new \WHMCS\Admin\ApplicationSupport\Route\Middleware\Authorization())->requireCsrfToken();
        }, "handle" => ["WHMCS\\Promotions\\PromotionController", "dismiss"]]]];
    }
    public function getDeferredRoutePathNameAttribute()
    {
        return "admin-promotions-";
    }
}

?>