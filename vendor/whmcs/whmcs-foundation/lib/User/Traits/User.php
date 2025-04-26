<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\User\Traits;

trait User
{
    public function getFullNameAttribute()
    {
        return $this->firstName . " " . $this->lastName;
    }
    public function currentIp()
    {
        return \WHMCS\Utility\Environment\CurrentRequest::getIP();
    }
    public function currentHostname()
    {
        return gethostbyaddr($this->currentIp());
    }
}

?>