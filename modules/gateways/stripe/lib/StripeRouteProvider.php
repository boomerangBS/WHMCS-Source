<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

namespace WHMCS\Module\Gateway\Stripe;

// Decoded file for php version 72.
class StripeRouteProvider implements \WHMCS\Route\Contracts\DeferredProviderInterface
{
    use \WHMCS\Route\ProviderTrait;
    protected function getRoutes()
    {
        return ["/stripe" => [["name" => $this->getDeferredRoutePathNameAttribute() . "payment-intent", "method" => ["POST"], "path" => "/payment/intent", "handle" => ["WHMCS\\Module\\Gateway\\Stripe\\StripeController", "intent"]], ["name" => $this->getDeferredRoutePathNameAttribute() . "payment-method-add", "method" => ["POST"], "path" => "/payment/add", "handle" => ["WHMCS\\Module\\Gateway\\Stripe\\StripeController", "add"]], ["name" => $this->getDeferredRoutePathNameAttribute() . "setup-intent", "method" => ["POST"], "path" => "/setup/intent", "handle" => ["WHMCS\\Module\\Gateway\\Stripe\\StripeController", "setupIntent"]]]];
    }
    public function getDeferredRoutePathNameAttribute()
    {
        return "stripe-";
    }
}

?>