<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Module\Mail\SmtpMail;

class SmtpOauth extends \PHPMailer\PHPMailer\OAuth
{
    protected $accessToken;
    protected function getToken()
    {
        $this->accessToken = parent::getToken();
        return $this->accessToken;
    }
    public function getSavedRefreshToken()
    {
        if($this->accessToken) {
            return $this->accessToken->getRefreshToken();
        }
        return NULL;
    }
}

?>