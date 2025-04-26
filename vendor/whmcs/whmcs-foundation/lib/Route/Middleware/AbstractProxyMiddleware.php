<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Route\Middleware;

abstract class AbstractProxyMiddleware implements \WHMCS\Route\Contracts\Middleware\ProxyInterface, \WHMCS\Route\Contracts\MapInterface
{
    use Strategy\AssumingMiddlewareTrait;
    use \WHMCS\Route\HandleMapTrait;
    public abstract function factoryProxyDriver($handle, \WHMCS\Http\Message\ServerRequest $request = NULL);
    public function _process(\WHMCS\Http\Message\ServerRequest $request, \Interop\Http\ServerMiddleware\DelegateInterface $delegate)
    {
        $handle = $request->getAttribute("matchedRouteHandle");
        if(!$handle) {
            return $delegate->process($request);
        }
        $mappedHandle = $this->getMappedRoute($handle);
        if(is_null($mappedHandle)) {
            return $delegate->process($request);
        }
        $driver = $this->factoryProxyDriver($mappedHandle, $request);
        if(!$driver instanceof \Interop\Http\ServerMiddleware\MiddlewareInterface) {
            throw new \RuntimeException("Invalid \"%s\" route attribute defined for %s", $this->getMappedAttributeName(), $request->getUri()->getPath());
        }
        return $driver->process($request, $delegate);
    }
}

?>