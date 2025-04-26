<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Route\Middleware\Strategy;

trait DelegatingMiddlewareTrait
{
    public abstract function _process(\WHMCS\Http\Message\ServerRequest $request, \Interop\Http\ServerMiddleware\DelegateInterface $delegate);
    public function process(\Psr\Http\Message\ServerRequestInterface $request, \Interop\Http\ServerMiddleware\DelegateInterface $delegate)
    {
        $result = $this->_process($request, $delegate);
        if($result instanceof \Psr\Http\Message\ResponseInterface || $result instanceof \WHMCS\Exception\HttpCodeException) {
            $response = $result;
        } else {
            $response = $delegate->process($result);
        }
        return $response;
    }
}

?>