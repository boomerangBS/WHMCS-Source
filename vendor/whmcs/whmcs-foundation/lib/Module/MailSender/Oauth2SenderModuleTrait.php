<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Module\MailSender;

trait Oauth2SenderModuleTrait
{
    protected $mailModule;
    public function setMailModuleInstance(\WHMCS\Module\Mail $mail) : void
    {
        $this->mailModule = $mail;
    }
    public function updateOauth2RefreshToken($refreshToken) : void
    {
        if($this->mailModule) {
            $this->mailModule->updateOauth2RefreshToken($refreshToken);
        }
    }
}

?>