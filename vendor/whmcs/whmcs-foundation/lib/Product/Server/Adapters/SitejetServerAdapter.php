<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Product\Server\Adapters;

class SitejetServerAdapter
{
    private $server;
    public static function factory(\WHMCS\Product\Server $server) : \self
    {
        $self = new static();
        $self->server = $server;
        return $self;
    }
    public static function getCompatibleServers() : \Illuminate\Support\Collection
    {
        return \WHMCS\Product\Server::enabled()->whereIn("type", ["cpanel", "plesk"])->get();
    }
    public static function getServersWithSitejetEnabled() : \Illuminate\Support\Collection
    {
        return \WHMCS\Product\Server::enabled()->with("remote")->get()->filter(function (\WHMCS\Product\Server $server) {
            if(is_null($server->remote) || is_null($server->remote->metaData) || !is_array($server->remote->metaData)) {
                return false;
            }
            return (bool) ($server->remote->metaData["sitejet_available"] ?? false);
        });
    }
    public static function getServersWithSitejetPackages() : \Illuminate\Support\Collection
    {
        return self::getServersWithSitejetEnabled()->filter(function (\WHMCS\Product\Server $server) {
            return self::factory($server)->getSitejetPackages();
        });
    }
    protected function isSitejetAvailable()
    {
        if(!$this->server->remote) {
            return false;
        }
        return !empty($this->server->remote->metaData["sitejet_available"]);
    }
    public function getSitejetPackages() : array
    {
        if(!$this->isSitejetAvailable()) {
            return [];
        }
        if(!$this->server->remote) {
            return [];
        }
        $sitejetPackages = $this->server->remote->metaData["sitejet_packages"];
        return is_array($sitejetPackages) ? $sitejetPackages : [];
    }
    public function hasSitejetForPackage($packageName)
    {
        return in_array($packageName, $this->getSitejetPackages(), true);
    }
}

?>