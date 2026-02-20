<?php

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