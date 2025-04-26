<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\User\Validation\ValidationCom\Provider;

class ValidationComClientRouteProvider implements \WHMCS\Route\Contracts\DeferredProviderInterface
{
    use \WHMCS\Route\ProviderTrait;
    public function getRoutes()
    {
        return ["/modules/gateways/callback/validation_com" => [["method" => ["POST"], "name" => $this->getDeferredRoutePathNameAttribute() . "event-callback", "path" => "/event", "handle" => ["WHMCS\\User\\Validation\\ValidationCom\\ValidationComController", "eventCallback"]], ["method" => ["POST"], "name" => $this->getDeferredRoutePathNameAttribute() . "link-callback", "path" => "/link", "handle" => ["WHMCS\\User\\Validation\\ValidationCom\\ValidationComController", "linkCallback"]], ["method" => ["GET", "POST"], "name" => $this->getDeferredRoutePathNameAttribute() . "link-complete-callback", "path" => "/link_complete", "handle" => ["WHMCS\\User\\Validation\\ValidationCom\\ValidationComController", "linkCompleteCallback"]], ["method" => ["GET", "POST"], "name" => $this->getDeferredRoutePathNameAttribute() . "client-complete-callback", "path" => "/client_complete", "handle" => ["WHMCS\\User\\Validation\\ValidationCom\\ValidationComController", "clientCompleteCallback"]]]];
    }
    public function getDeferredRoutePathNameAttribute()
    {
        return "validation_com-";
    }
}

?>