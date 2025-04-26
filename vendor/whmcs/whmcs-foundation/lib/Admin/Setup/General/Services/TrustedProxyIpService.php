<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Admin\Setup\General\Services;

class TrustedProxyIpService
{
    public function add($ip) : void
    {
        if($this->isInvalidStructure($ip)) {
            throw new \InvalidArgumentException();
        }
        $this->addBatch(collect()->add($ip));
    }
    public function addBatch(\Illuminate\Support\Collection $ips) : void
    {
        $hasInvalidStructure = $ips->contains(function ($ip) {
            return $this->isInvalidStructure($ip);
        });
        if($hasInvalidStructure) {
            throw new \InvalidArgumentException();
        }
        $whmcs = \App::self();
        $trustedProxyIps = $whmcs->get_config("trustedProxyIps");
        $trustedProxyIps = json_decode($trustedProxyIps, true);
        $trustedProxyIps = is_array($trustedProxyIps) ? $trustedProxyIps : [];
        $trustedProxyIps = collect($trustedProxyIps)->merge($ips)->unique("ip");
        $remoteIp = $whmcs->getRemoteIp();
        $whmcs->set_config("trustedProxyIps", $trustedProxyIps->toJson());
        if($this->doesntHaveRemoteIp($remoteIp, $ips)) {
            return NULL;
        }
        \WHMCS\Http\Request::defineProxyTrustFromApplication($whmcs);
        $whmcs->setRemoteIp(\WHMCS\Utility\Environment\CurrentRequest::getIP());
        $auth = new \WHMCS\Auth();
        $auth->getInfobyID(\WHMCS\Session::get("adminid"));
        $auth->setSessionVars($whmcs);
    }
    private function hasRemoteIp($ipToFind, $ips) : \Illuminate\Support\Collection
    {
        return $ips->contains(function (array $ip) use($ipToFind) {
            return \WHMCS\Http\IpUtils::checkIp($ipToFind, $ip["ip"]);
        });
    }
    private function doesntHaveRemoteIp($ipToFind, $ips) : \Illuminate\Support\Collection
    {
        return !$this->hasRemoteIp($ipToFind, $ips);
    }
    private function isValidStructure($ip) : array
    {
        return isset($ip["ip"]) && isset($ip["note"]);
    }
    private function isInvalidStructure($ip) : array
    {
        return !$this->isValidStructure($ip);
    }
}

?>