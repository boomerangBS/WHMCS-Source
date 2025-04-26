<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Route\Middleware;

class RoutePathMatch implements \WHMCS\Route\Contracts\Middleware\StrategyInterface
{
    use Strategy\AssumingMiddlewareTrait;
    const ATTRIBUTE_ROUTE_HANDLE = "matchedRouteHandle";
    public function _process(\WHMCS\Http\Message\ServerRequest $request, \Interop\Http\ServerMiddleware\DelegateInterface $delegate)
    {
        $dispatch = \DI::make("Route\\Dispatch");
        $route = $dispatch->dispatch($request->getMethod(), $request->getUri()->getPath());
        if($route[0] == $dispatch::FOUND) {
            if(!empty($route[2])) {
                foreach ($route[2] as $attribute => $value) {
                    $request = $request->withAttribute($attribute, $value);
                }
            }
            $request = $request->withAttribute(static::ATTRIBUTE_ROUTE_HANDLE, $route[1]);
        }
        return $delegate->process($request);
    }
}

?>