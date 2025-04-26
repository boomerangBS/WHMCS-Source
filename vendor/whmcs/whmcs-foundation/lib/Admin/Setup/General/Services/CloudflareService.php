<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Admin\Setup\General\Services;

class CloudflareService
{
    public function fetchIps() : \Illuminate\Support\Collection
    {
        $ips = collect();
        $client = new \GuzzleHttp\Client();
        $response = $client->get("https://api.cloudflare.com/client/v4/ips");
        if($response->getStatusCode() !== 200) {
            return $ips;
        }
        $content = json_decode($response->getBody()->getContents(), true);
        return collect($content["result"]["ipv4_cidrs"])->merge($content["result"]["ipv6_cidrs"]);
    }
}

?>