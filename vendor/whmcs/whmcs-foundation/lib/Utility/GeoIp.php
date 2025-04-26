<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

namespace WHMCS\Utility;

// Decoded file for php version 72.
class GeoIp
{
    public static function getLookupUrl($ip)
    {
        $ip = preg_replace("/[^a-z0-9:\\.]/i", "", $ip);
        $link = "https://extreme-ip-lookup.com/" . $ip;
        return $link;
    }
    public static function getLookupHtmlAnchor($ip, $classes = NULL, $text = NULL)
    {
        $link = static::getLookupUrl($ip);
        if(is_null($classes)) {
            $classes .= "autoLinked";
        } elseif($classes && is_string($classes)) {
            $classes .= " autoLinked";
        } else {
            $classes = "";
        }
        $text = (string) $text;
        if(!strlen($text)) {
            $text = $ip;
        }
        return sprintf("<a href=\"%s\" class=\"%s\" >%s</a>", $link, $classes, $text);
    }
}

?>