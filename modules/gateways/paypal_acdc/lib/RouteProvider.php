<?php

namespace WHMCS\Module\Gateway\paypal_acdc;

class RouteProvider implements \WHMCS\Route\Contracts\DeferredProviderInterface
{
    use \WHMCS\Route\ProviderTrait;
    public function getRoutes()
    {
        return ["/paypal_acdc" => [["name" => $this->getDeferredRoutePathNameAttribute() . "create-order", "method" => ["POST"], "path" => "/order/create", "handle" => ["WHMCS\\Module\\Gateway\\paypal_acdc\\RouteController", "createOrder"]], ["name" => $this->getDeferredRoutePathNameAttribute() . "invoice-on-approve", "method" => ["POST"], "path" => "/invoice/verify/payment", "handle" => ["WHMCS\\Module\\Gateway\\paypal_acdc\\RouteController", "invoiceOnApprove"], "authorization" => function () {
            return (new \WHMCS\ClientArea\ApplicationSupport\Route\Middleware\Authorization())->requireCsrfToken();
        }], ["name" => $this->getDeferredRoutePathNameAttribute() . "create-setup-token", "method" => ["POST"], "path" => "/setup-token/create", "handle" => ["WHMCS\\Module\\Gateway\\paypal_acdc\\RouteController", "createSetupToken"], "authorization" => function () {
            return (new \WHMCS\ClientArea\ApplicationSupport\Route\Middleware\Authorization())->requireCsrfToken();
        }], ["name" => $this->getDeferredRoutePathNameAttribute() . "create-payment-token", "method" => ["POST"], "path" => "/payment-token/create", "handle" => ["WHMCS\\Module\\Gateway\\paypal_acdc\\RouteController", "createPaymentToken"], "authorization" => function () {
            return (new \WHMCS\ClientArea\ApplicationSupport\Route\Middleware\Authorization())->requireCsrfToken();
        }]]];
    }
    public function getDeferredRoutePathNameAttribute()
    {
        return "paypal_acdc-";
    }
}

?>