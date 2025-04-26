<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Filter;

class HostAddress
{
    private $ipAddress = "";
    private $hostname = "";
    private $isIpv6 = false;
    private $port = "";
    public function __construct($hostname, $ipAddress = "", $port = "")
    {
        if(empty($hostname) && empty($ipAddress)) {
            throw new \WHMCS\Exception\Validation\InvalidHostAddress();
        }
        if(!empty($hostname)) {
            $this->setHostname($hostname);
        }
        if(!empty($ipAddress)) {
            $this->setIpAddress($ipAddress);
        }
        if(!empty($port) && empty($this->port)) {
            $this->setPort($port);
        }
    }
    public function getIpAddress()
    {
        return $this->ipAddress;
    }
    public function getIpAddressAsHost()
    {
        $ipAddress = $this->getIpAddress();
        if($ipAddress && $this->isIpv6) {
            return sprintf("[%s]", $ipAddress);
        }
        return $ipAddress;
    }
    public function getHostname()
    {
        return $this->hostname ?: $this->getIpAddress();
    }
    public function getHost()
    {
        return $this->hostname ?: $this->getIpAddressAsHost();
    }
    public function getPort()
    {
        return $this->port;
    }
    private function parseAddress($address) : array
    {
        $address = trim($address);
        if(strpos($address, "[") !== false && strpos($address, "]") !== false) {
            $addressParts = explode("]:", $address);
            if(count($addressParts) == 2) {
                return [trim($addressParts[0], "[]"), $addressParts[1]];
            }
            return [trim($address, "[]"), ""];
        }
        $addressParts = explode(":", $address);
        if(count($addressParts) == 2) {
            return $addressParts;
        }
        return [$address, ""];
    }
    private function sanitizePort($port)
    {
        if(is_string($port)) {
            $port = trim($port, "\t\n\r\0\v:");
            if(!ctype_digit($port)) {
                return false;
            }
        }
        $port = (int) $port;
        if($port < 0 || 65535 < $port) {
            return false;
        }
        return $port;
    }
    private function setPort($port)
    {
        $port = $this->sanitizePort($port);
        if($port === false) {
            throw new \WHMCS\Exception\Validation\InvalidPort();
        }
        $this->port = $port;
    }
    private function setIpAddress($ipAddress)
    {
        list($ipAddress, $addressPort) = $this->parseAddress($ipAddress);
        if($ipAddress === "") {
            return NULL;
        }
        $this->isIpv6 = \WHMCS\Http\IpUtils::isValidIPv6($ipAddress);
        if(!$this->isIpv6 && !\WHMCS\Http\IpUtils::isValidIPv4($ipAddress)) {
            throw new \WHMCS\Exception\Validation\InvalidIpAddress();
        }
        if(!empty($addressPort)) {
            $this->setPort($addressPort);
        }
        $this->ipAddress = $ipAddress;
        return $this;
    }
    private function setHostname($hostname)
    {
        list($hostname, $hostnamePort) = $this->parseAddress($hostname);
        if($hostname === "") {
            return NULL;
        }
        if(!empty($hostnamePort)) {
            $this->setPort($hostnamePort);
        }
        if(\WHMCS\Http\IpUtils::isValidIPv6($hostname)) {
            $this->setIpAddress($hostname);
            return $this;
        }
        if(filter_var($hostname, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME) === false) {
            throw new \WHMCS\Exception\Validation\InvalidHostname();
        }
        $this->hostname = $hostname;
        return $this;
    }
}

?>