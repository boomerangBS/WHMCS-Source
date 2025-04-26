<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Service\Adapters;

class AbstractServiceAdapter
{
    protected $service;
    public static function factory(\WHMCS\Service\Service $service) : \self
    {
        $self = new static();
        $self->service = $service;
        return $self;
    }
    protected function moduleCall(string $functionName, array $params = [])
    {
        $server = new \WHMCS\Module\Server();
        $server->loadByServiceID($this->service->id);
        $callResult = $server->call($functionName, array_merge($server->getServerParams($this->service->serverModel), $params));
        if(is_string($callResult)) {
            $moduleConfigFailures = [\WHMCS\Module\AbstractModule::MODULE_NOT_ACTIVE, \WHMCS\Module\AbstractModule::FUNCTIONDOESNTEXIST];
            if(in_array($callResult, $moduleConfigFailures, true)) {
                throw new \WHMCS\Exception\Module\NotServicable("Invalid configuration (inactive module or function does not exist)");
            }
            throw new \WHMCS\Exception\Module\NotServicable("Invalid return from " . $functionName . ": " . $callResult);
        }
        return $callResult;
    }
}

?>