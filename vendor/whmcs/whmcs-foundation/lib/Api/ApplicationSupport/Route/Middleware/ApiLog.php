<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Api\ApplicationSupport\Route\Middleware;

class ApiLog implements \WHMCS\Route\Contracts\Middleware\StrategyInterface
{
    use \WHMCS\Route\Middleware\Strategy\AssumingMiddlewareTrait;
    public function _process(\WHMCS\Http\Message\ServerRequest $request, \Interop\Http\ServerMiddleware\DelegateInterface $delegate)
    {
        $response = $delegate->process($request);
        $loggableRequest = \DI::make("runtimeStorage")->apiRequest;
        if(!$loggableRequest) {
            $loggableRequest = $request;
        }
        $logger = \DI::make("ApiLog");
        $logger->info($loggableRequest->getAction(), ["request" => $loggableRequest, "response" => $response]);
        return $response;
    }
}

?>