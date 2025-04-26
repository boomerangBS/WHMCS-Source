<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\ClientArea\Invoice;

class InvoiceRouteProvider implements \WHMCS\Route\Contracts\DeferredProviderInterface
{
    use \WHMCS\Route\ProviderTrait;
    public function getRoutes()
    {
        return ["/invoice" => [["name" => $this->getDeferredRoutePathNameAttribute() . "pay", "method" => ["GET", "POST"], "path" => "/{id:\\d+}/pay", "handle" => ["WHMCS\\ClientArea\\Invoice\\InvoiceController", "pay"]], ["name" => $this->getDeferredRoutePathNameAttribute() . "pay-process", "method" => ["POST"], "path" => "/{id:\\d+}/process", "authorization" => function () {
            return (new \WHMCS\ClientArea\ApplicationSupport\Route\Middleware\Authorization())->requireCsrfToken();
        }, "handle" => ["WHMCS\\ClientArea\\Invoice\\InvoiceController", "process"]]]];
    }
    public function getDeferredRoutePathNameAttribute()
    {
        return "invoice-";
    }
}

?>