<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Module\Security\Totp\Generator;

interface GeneratorInterface
{
    public static function hasDependenciesMet();
    public function generate($accountName, string $secret, string $issuer);
    public function size() : int;
}

?>