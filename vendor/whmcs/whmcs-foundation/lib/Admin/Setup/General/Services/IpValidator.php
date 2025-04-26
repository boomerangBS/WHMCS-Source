<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Admin\Setup\General\Services;

class IpValidator
{
    public function isValid($ipAddress)
    {
        if(strpos($ipAddress, "/") !== false) {
            list($ip, $netmask) = explode("/", $ipAddress, 2);
            return \WHMCS\Http\IpUtils::checkIp($ip, $ipAddress);
        }
        return filter_var($ipAddress, FILTER_VALIDATE_IP);
    }
    public function isInvalid($ipAddress)
    {
        return !$this->isValid($ipAddress);
    }
}

?>