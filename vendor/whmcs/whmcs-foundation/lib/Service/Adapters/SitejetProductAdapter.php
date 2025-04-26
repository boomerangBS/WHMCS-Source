<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Service\Adapters;

class SitejetProductAdapter extends AbstractProductAdapter
{
    use SitejetProductAwareTrait;
    private static $defaultServersPerGroup = [];
    private static $defaultServersPerModule = [];
    protected function getProduct() : \WHMCS\Product\Product
    {
        return $this->product;
    }
    protected function getDefaultServer() : \WHMCS\Product\Server
    {
        if($this->product->serverGroupId) {
            if(!array_key_exists($this->product->serverGroupId, self::$defaultServersPerGroup)) {
                $server = NULL;
                $serverGroup = \WHMCS\Product\Server\Group::find($this->product->serverGroupId);
                if($serverGroup) {
                    $server = $serverGroup->getDefaultServer();
                }
                self::$defaultServersPerGroup[$this->product->serverGroupId] = $server;
            }
            return self::$defaultServersPerGroup[$this->product->serverGroupId];
        }
        if($this->product->module) {
            if(!array_key_exists($this->product->module, self::$defaultServersPerModule)) {
                $server = \WHMCS\Product\Server::ofModule($this->product->module)->default()->first();
                self::$defaultServersPerModule[$this->product->module] = $server;
            }
            return self::$defaultServersPerModule[$this->product->module];
        }
        return NULL;
    }
    public function hasSitejetAvailable()
    {
        $serverPackageName = $this->product->moduleConfigOption1;
        $defaultServer = $this->getDefaultServer();
        if(!$serverPackageName || !$defaultServer) {
            return false;
        }
        return \WHMCS\Product\Server\Adapters\SitejetServerAdapter::factory($defaultServer)->hasSitejetForPackage($serverPackageName);
    }
}

?>