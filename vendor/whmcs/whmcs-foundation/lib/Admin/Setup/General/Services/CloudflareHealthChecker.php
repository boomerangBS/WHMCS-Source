<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Admin\Setup\General\Services;

class CloudflareHealthChecker
{
    private $cloudflareService;
    public function __construct(CloudflareService $cloudflareService = NULL)
    {
        $this->cloudflareService = $cloudflareService ?? \DI::make("WHMCS\\Admin\\Setup\\General\\Services\\CloudflareService");
    }
    public function hasCloudflareHeader()
    {
        $cloudflareHeaders = collect(["CF-Connecting-IP", "CF-Connecting-IPv6", "CF-EW-Via", "CF-Pseudo-IPv4", "CF-RAY", "CF-IPCountry", "CF-Visitor", "CF-Worker"]);
        return $cloudflareHeaders->contains(function ($header) {
            $serverHeader = \Illuminate\Support\Str::of($header)->upper()->replace("-", "_")->prepend("HTTP_")->__toString();
            return isset($_SERVER[$serverHeader]);
        });
    }
    public function isCloudFlareIpEqualsResolvedIp()
    {
        $resolvedIp = \WHMCS\Utility\Environment\CurrentRequest::getIP();
        if(($_SERVER["HTTP_CF_CONNECTING_IP"] ?? NULL) === $resolvedIp) {
            return true;
        }
        if(($_SERVER["HTTP_CF_CONNECTING_IPV6"] ?? NULL) === $resolvedIp) {
            return true;
        }
        return false;
    }
    public function isCloudFlareIpProvided()
    {
        return isset($_SERVER["HTTP_CF_CONNECTING_IP"]) || isset($_SERVER["HTTP_CF_CONNECTING_IPV6"]);
    }
    public function findCloudflareIpHeader()
    {
        return collect(["CF_CONNECTING_IPV6", "CF_CONNECTING_IP"])->first(function ($header) {
            return isset($_SERVER["HTTP_" . $header]);
        });
    }
}

?>