<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Mail\Incoming;

trait MailboxOauthTokenTrait
{
    protected $isTest = false;
    protected function getOauth2AccessToken(\WHMCS\Support\Department $department) : \WHMCS\Support\Department
    {
        $handler = new \WHMCS\Mail\MailAuthHandler();
        $provider = $handler->createProvider($department->mailAuthConfig["service_provider"], $department->mailAuthConfig["oauth2_client_id"], $department->mailAuthConfig["oauth2_client_secret"], \WHMCS\Mail\MailAuthHandler::CONTEXT_SUPPORT_DEPARTMENT);
        $accessToken = $provider->getAccessToken(new \League\OAuth2\Client\Grant\RefreshToken(), ["refresh_token" => $department->mailAuthConfig["oauth2_refresh_token"]]);
        if(!$this->isTest) {
            $updatedRefreshToken = $accessToken->getRefreshToken();
            if(!is_null($updatedRefreshToken)) {
                $department->mailAuthConfig = array_merge($department->mailAuthConfig, ["oauth2_refresh_token" => $updatedRefreshToken]);
                $department->save();
            }
        }
        return $accessToken;
    }
}

?>