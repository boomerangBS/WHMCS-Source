<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

namespace WHMCS\Module\Gateway\paypal_ppcpv;

// Decoded file for php version 72.
class RouteProvider implements \WHMCS\Route\Contracts\DeferredProviderInterface
{
    use \WHMCS\Route\ProviderTrait;
    public function getRoutes()
    {
        $module = PayPalCommerce::MODULE_NAME;
        return ["/" . $module => [["name" => $this->getDeferredRoutePathNameAttribute() . "create-order", "method" => ["POST"], "path" => "/order/create", "handle" => ["WHMCS\\Module\\Gateway\\paypal_ppcpv\\RouteController", "createOrder"], "authorization" => function () {
            return new \WHMCS\ClientArea\ApplicationSupport\Route\Middleware\Authorization();
        }], ["name" => $this->getDeferredRoutePathNameAttribute() . "on-approve", "method" => ["POST"], "path" => "/verify/payment", "handle" => ["WHMCS\\Module\\Gateway\\paypal_ppcpv\\RouteController", "invoiceOnApprove"], "authorization" => function () {
            return new \WHMCS\ClientArea\ApplicationSupport\Route\Middleware\Authorization();
        }], ["name" => $this->getDeferredRoutePathNameAttribute() . "create-setup-token", "method" => ["POST"], "path" => "/setup-token/create", "handle" => ["WHMCS\\Module\\Gateway\\paypal_ppcpv\\RouteController", "createSetupToken"], "authorization" => function () {
            return (new \WHMCS\ClientArea\ApplicationSupport\Route\Middleware\Authorization())->requireCsrfToken();
        }], ["name" => $this->getDeferredRoutePathNameAttribute() . "get-setup-token", "method" => ["POST"], "path" => "/setup-token/get", "handle" => ["WHMCS\\Module\\Gateway\\paypal_ppcpv\\RouteController", "getSetupToken"], "authorization" => function () {
            return (new \WHMCS\ClientArea\ApplicationSupport\Route\Middleware\Authorization())->requireCsrfToken();
        }]]];
    }
    public function getDeferredRoutePathNameAttribute()
    {
        return PayPalCommerce::MODULE_NAME . "-";
    }
}

?>