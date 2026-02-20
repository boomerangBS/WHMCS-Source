<?php

namespace WHMCS\Route\Middleware;

class AuthenticationProxy extends AbstractProxyMiddleware
{
    public function getMappedAttributeName()
    {
        return "authentication";
    }
    public function factoryProxyDriver($handle, \WHMCS\Http\Message\ServerRequest $request = NULL)
    {
        if($handle == "api") {
            $driver = new \WHMCS\Api\ApplicationSupport\Route\Middleware\Authentication();
        } elseif($handle == "admin") {
            $driver = new \WHMCS\Admin\ApplicationSupport\Route\Middleware\Authentication();
        } elseif($handle == "adminConfirmation") {
            $driver = new \WHMCS\Admin\ApplicationSupport\Route\Middleware\AuthenticationConfirmation();
        } elseif($handle == "clientarea") {
            $driver = new \WHMCS\ClientArea\ApplicationSupport\Route\Middleware\Authentication();
        } elseif($handle == "token") {
            $driver = new \WHMCS\Admin\ApplicationSupport\Route\Middleware\TokenAuth();
        } elseif(is_callable($handle)) {
            $driver = $handle();
        } else {
            throw new \RuntimeException("blank or non admin/api authentication middleware not supported");
        }
        return $driver;
    }
}

?>