<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Api\NG\Versions\V2\Middleware;

class ApiNgStateProcessor implements \WHMCS\Route\Contracts\Middleware\StrategyInterface
{
    use \WHMCS\Route\Middleware\Strategy\AssumingMiddlewareTrait;
    public function _process(\WHMCS\Http\Message\ServerRequest $request, \Interop\Http\ServerMiddleware\DelegateInterface $delegate)
    {
        $request = (new \WHMCS\Api\NG\Versions\V2\State\ApiRequestStateHandler())->createForRequest($request);
        $response = $delegate->process($request);
        return $request->getState()->addToResponse($response);
    }
}

?>