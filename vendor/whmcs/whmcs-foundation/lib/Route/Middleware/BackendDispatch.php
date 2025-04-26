<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Route\Middleware;

class BackendDispatch implements \WHMCS\Route\Contracts\Middleware\StrategyInterface
{
    use Strategy\AssumingMiddlewareTrait;
    public function _process(\WHMCS\Http\Message\ServerRequest $request, \Interop\Http\ServerMiddleware\DelegateInterface $delegate)
    {
        return $this->getDispatch($request)->dispatch($request);
    }
    public function getDispatch(\WHMCS\Http\Message\ServerRequest $request)
    {
        if($request->isAdminRequest()) {
            return \DI::make("Backend\\Dispatcher\\Admin");
        }
        if($request->isApiV1Request()) {
            return \DI::make("Backend\\Dispatcher\\Api\\V1");
        }
        if($request->isApiNGRequest()) {
            return \DI::make("Backend\\Dispatcher\\Api\\NG");
        }
        return \DI::make("Backend\\Dispatcher\\Client");
    }
}

?>