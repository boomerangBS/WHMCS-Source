<?php

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