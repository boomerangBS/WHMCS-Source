<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Module\Contracts;

interface SenderModuleInterface
{
    public function settings();
    public function getName();
    public function getDisplayName();
    public function testConnection(array $params);
    public function send(array $params, \WHMCS\Mail\Message $message);
}

?>