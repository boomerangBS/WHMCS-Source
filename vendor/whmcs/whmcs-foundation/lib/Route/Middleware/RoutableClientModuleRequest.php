<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Route\Middleware;

class RoutableClientModuleRequest implements \WHMCS\Route\Contracts\Middleware\StrategyInterface
{
    use Strategy\AssumingMiddlewareTrait;
    public function _process(\WHMCS\Http\Message\ServerRequest $request, \Interop\Http\ServerMiddleware\DelegateInterface $delegate)
    {
        if(!$request->isAdminRequest() && ($moduleName = $request->get("m", ""))) {
            $moduleName = preg_replace("/[^a-zA-Z0-9._]/", "", $moduleName);
            $addonModule = new \WHMCS\Module\Addon();
            if(!$addonModule->load($moduleName) || !$addonModule->functionExists("clientarea")) {
                $controller = new \WHMCS\ClientArea\ClientAreaController();
                return $controller->homePage($request);
            }
            $uri = $request->getUri()->withPath("/clientarea/module/" . $moduleName);
            $request = $request->withUri($uri);
        }
        return $delegate->process($request);
    }
}

?>