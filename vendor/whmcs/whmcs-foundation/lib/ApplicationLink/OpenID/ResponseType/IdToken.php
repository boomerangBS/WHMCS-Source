<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\ApplicationLink\OpenID\ResponseType;

class IdToken extends \OAuth2\OpenID\ResponseType\IdToken
{
    protected function encodeToken(array $token, $client_id = NULL)
    {
        $key = $this->publicKeyStorage->getKeyDetails($client_id);
        return $this->encryptionUtil->encode($token, $key["privateKey"], $key["algorithm"], $key["identifier"]);
    }
}

?>