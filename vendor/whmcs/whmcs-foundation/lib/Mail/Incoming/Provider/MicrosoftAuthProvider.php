<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Mail\Incoming\Provider;

class MicrosoftAuthProvider extends \Stevenmaguire\OAuth2\Client\Provider\Microsoft implements MailAuthProviderInterface
{
    protected $urlAuthorize = "https://login.microsoftonline.com/common/oauth2/v2.0/authorize";
    protected $urlAccessToken = "https://login.microsoftonline.com/common/oauth2/v2.0/token";
    const SCOPES = ["offline_access", "User.Read", "Mail.Send", "Mail.ReadWrite"];
    protected function getScopeSeparator()
    {
        return " ";
    }
    protected function getAuthorizationParameters(array $options)
    {
        $options["prompt"] = "consent";
        return parent::getAuthorizationParameters($options);
    }
    protected function getAccessTokenRequest(array $params)
    {
        $params["scope"] = implode(" ", self::SCOPES);
        return parent::getAccessTokenRequest($params);
    }
    public static function getSupportedAuthTypes() : array
    {
        return [\WHMCS\Mail\MailAuthHandler::AUTH_TYPE_OAUTH2];
    }
    public static function supportsLegacyMailProtocols()
    {
        return false;
    }
    public function clearOpposingAuthData(\WHMCS\Support\Department $department) : void
    {
        $department->host = "";
        $department->login = "";
        $department->port = "";
        $department->password = "";
    }
}

?>