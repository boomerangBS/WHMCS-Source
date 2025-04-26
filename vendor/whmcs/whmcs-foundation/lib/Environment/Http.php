<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Environment;

class Http
{
    public function siteIsConfiguredForSsl()
    {
        try {
            \App::getSystemSSLURLOrFail();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
    public function siteHasVerifiedSslCert()
    {
        try {
            $url = \App::getSystemSSLURLOrFail();
            $whmcsHeaderVersion = \App::getVersion()->getMajor();
            $request = new \WHMCS\Http\Client\HttpClient(["verify" => true]);
            $request->get($url, ["headers" => ["User-Agent" => "WHMCS/" . $whmcsHeaderVersion]]);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}

?>