<?php

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