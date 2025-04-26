<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Route\Middleware;

class AuthorizationProxy extends AbstractProxyMiddleware
{
    public function getMappedAttributeName()
    {
        return "authorization";
    }
    public function factoryProxyDriver($handle, \WHMCS\Http\Message\ServerRequest $request = NULL)
    {
        if($handle == "api") {
            $driver = new \WHMCS\Api\ApplicationSupport\Route\Middleware\Authorization();
        } elseif(is_callable($handle)) {
            $driver = $handle();
        } else {
            throw new \RuntimeException("Invalid authorization middleware not supported" . $request->getUri()->getPath());
        }
        return $driver;
    }
}

?>