<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Environment;

interface ComponentInterface
{
    public function report(Report $report);
    public function addTopic($name, $closure);
    public function name();
}

?>