<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Authentication\Contracts;

interface Token
{
    public static function factoryFromUser(\WHMCS\User\User $user);
    public function validFormat();
    public function id() : int;
    public function generate();
    public function generateHash();
    public function validateUser(\WHMCS\User\User $user, $validateIp) : \WHMCS\User\User;
}

?>