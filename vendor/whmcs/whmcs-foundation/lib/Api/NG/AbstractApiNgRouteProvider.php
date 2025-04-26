<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Api\NG;

abstract class AbstractApiNgRouteProvider implements \WHMCS\Route\Contracts\ProviderInterface, \WHMCS\Route\Contracts\DeferredProviderInterface
{
    use \WHMCS\Route\ProviderTrait;
    const API_VERSION = NULL;
    public abstract function getMiddlewareStack() : array;
    public function getRoutePathPrefix()
    {
        if(is_null(static::API_VERSION)) {
            throw new \WHMCS\Exception\Api\NG\ApiNgException("Invalid API_VERSION");
        }
        return "/api/" . static::API_VERSION;
    }
    public function getDeferredRoutePathNameAttribute()
    {
        return "api-ng-" . $this->getRoutePathPrefix() . "-";
    }
}

?>