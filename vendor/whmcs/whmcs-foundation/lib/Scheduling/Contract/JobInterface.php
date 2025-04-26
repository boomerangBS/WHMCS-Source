<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Scheduling\Contract;

interface JobInterface
{
    public function jobName($name);
    public function jobClassName($className);
    public function jobMethodName($methodName);
    public function jobMethodArguments($arguments);
    public function jobAvailableAt(\WHMCS\Carbon $date);
    public function jobDigestHash($hash);
}

?>