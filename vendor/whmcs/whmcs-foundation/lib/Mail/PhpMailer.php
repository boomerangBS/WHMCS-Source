<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Mail;

class PhpMailer extends \PHPMailer\PHPMailer\PHPMailer
{
    protected static $validEncodings = ["8bit", "7bit", "binary", "base64", "quoted-printable"];
    public function setEncoding($config_value) : void
    {
        $validEncodings = self::$validEncodings;
        if(isset($config_value) && !empty($validEncodings[$config_value])) {
            $this->Encoding = $validEncodings[$config_value];
        } else {
            $this->Encoding = $validEncodings[0];
        }
    }
    public function serverHostname()
    {
        $hostname = parent::serverHostname();
        if(!$hostname || $hostname === "localhost.localdomain") {
            $hostname = parse_url(\WHMCS\Config\Setting::getValue("Domain"), PHP_URL_HOST);
        }
        return $hostname;
    }
    public function setSenderNameAndEmail($name, $email) : \self
    {
        if(!$name) {
            $name = \WHMCS\Config\Setting::getValue("CompanyName");
        }
        if(!$email) {
            $email = \WHMCS\Config\Setting::getValue("Email");
        }
        $this->From = $email;
        $this->FromName = \WHMCS\Input\Sanitize::decode($name);
        $this->Sender = $email;
        return $this;
    }
    public static function getValidEncodings() : array
    {
        return self::$validEncodings;
    }
    public function getRfcMailContent()
    {
        return trim($this->MIMEHeader, "\r\n") . static::$LE . static::$LE . $this->MIMEBody;
    }
}

?>