<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Api\ApplicationSupport\Route\Middleware;

class SystemAccessControl implements \WHMCS\Route\Contracts\Middleware\StrategyInterface
{
    use \WHMCS\Route\Middleware\Strategy\AssumingMiddlewareTrait;
    protected function getSystemAccessKey()
    {
        $config = \DI::make("config");
        return $config["api_access_key"];
    }
    protected function getAllowedIps()
    {
        $allowedIps = safe_unserialize(\WHMCS\Config\Setting::getValue("APIAllowedIPs"));
        $cleanedIps = [];
        foreach ($allowedIps as $key => $allowedIp) {
            if(!empty($allowedIp["ip"]) && trim($allowedIp["ip"])) {
                $cleanedIps[] = trim($allowedIp["ip"]);
            }
        }
        return $cleanedIps;
    }
    public function _process(\WHMCS\Http\Message\ServerRequest $request, \Interop\Http\ServerMiddleware\DelegateInterface $delegate)
    {
        $accessKey = $request->getAccessKey();
        if(\App::isVisitorIPBanned()) {
            throw new \WHMCS\Exception\Api\AuthException("IP Banned");
        }
        $systemAccessKey = $this->getSystemAccessKey();
        if(!empty($systemAccessKey) && $accessKey) {
            if($accessKey != $systemAccessKey) {
                throw new \WHMCS\Exception\Api\AuthException("Invalid Access Key");
            }
        } elseif(!in_array(\App::getRemoteIp(), $this->getAllowedIps())) {
            throw new \WHMCS\Exception\Api\AuthException("Invalid IP " . \App::getRemoteIp());
        }
        return $delegate->process($request);
    }
}

?>