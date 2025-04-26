<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Admin\ApplicationSupport\Route\Middleware;

class Authorization extends \WHMCS\Security\Middleware\Authorization
{
    public function getDefaultCsrfNamespace()
    {
        return "WHMCS.admin.default";
    }
    protected function responseMissingMultiplePermissions(array $permissionNames = [])
    {
        return (new \WHMCS\Admin\ApplicationSupport\Http\Message\ResponseFactory())->missingPermission($this->getRequest(), $permissionNames, true);
    }
    protected function responseMissingPermission(array $permissionNames = [])
    {
        return (new \WHMCS\Admin\ApplicationSupport\Http\Message\ResponseFactory())->missingPermission($this->getRequest(), $permissionNames, false);
    }
    protected function responseInvalidCsrfToken()
    {
        return (new \WHMCS\Admin\ApplicationSupport\Http\Message\ResponseFactory())->invalidCsrfToken($this->getRequest());
    }
}

?>