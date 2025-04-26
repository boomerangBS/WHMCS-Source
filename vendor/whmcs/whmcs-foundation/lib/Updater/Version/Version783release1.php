<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Updater\Version;

class Version783release1 extends IncrementalVersion
{
    protected $updateActions = ["convertCentovaCastHostnames"];
    protected function convertCentovaCastHostnames()
    {
        $servers = \WHMCS\Product\Server::ofModule("centovacast")->get();
        foreach ($servers as $server) {
            $url = $server->hostname;
            if(!preg_match("#^https?://#", $url)) {
            } else {
                $parts = parse_url($url);
                $server->hostname = $parts["host"];
                $server->port = $parts["port"];
                $server->accessHash = $parts["path"];
                $server->secure = strtolower($parts["scheme"]) === "https" ? "on" : "";
                $server->save();
            }
        }
    }
}

?>