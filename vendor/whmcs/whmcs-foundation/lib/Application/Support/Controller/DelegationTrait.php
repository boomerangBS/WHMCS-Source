<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Application\Support\Controller;

trait DelegationTrait
{
    public function redirect(\WHMCS\Http\Message\ServerRequest $request)
    {
        return new \Laminas\Diactoros\Response\RedirectResponse($request->getAttribute("redirect"));
    }
    public function redirectTo($pathData, \WHMCS\Http\Message\ServerRequest $request)
    {
        $pathVars = [];
        if(is_array($pathData)) {
            list($pathName, $pathVars) = $pathData;
        } else {
            $pathName = $pathData;
        }
        return $this->redirect($request->withAttribute("redirect", routePath($pathName, $pathVars)));
    }
    protected function delegateTo($pathData, \WHMCS\Http\Message\ServerRequest $request)
    {
        $pathVars = [];
        if(is_array($pathData)) {
            list($pathName, $pathVars) = $pathData;
        } else {
            $pathName = $pathData;
        }
        $request = $request->withUri($request->getUri()->withPath(\DI::make("Route\\UriPath")->getRawPath($pathName, $pathVars)));
        return (new \WHMCS\Route\Middleware\BackendDispatch())->getDispatch($request)->dispatch($request);
    }
}

?>