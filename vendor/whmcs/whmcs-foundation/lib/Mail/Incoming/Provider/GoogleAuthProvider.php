<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Mail\Incoming\Provider;

class GoogleAuthProvider extends \League\OAuth2\Client\Provider\Google implements MailAuthProviderInterface
{
    public static function getSupportedAuthTypes() : array
    {
        return [\WHMCS\Mail\MailAuthHandler::AUTH_TYPE_PLAIN, \WHMCS\Mail\MailAuthHandler::AUTH_TYPE_OAUTH2];
    }
    public static function supportsLegacyMailProtocols()
    {
        return true;
    }
    public function clearOpposingAuthData(\WHMCS\Support\Department $department) : void
    {
        $department->password = "";
    }
}

?>