<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Authentication\Remote;

class AuthUserMetadata
{
    protected $fullName;
    protected $emailAddress;
    protected $username;
    protected $providerName;
    public function __construct($fullName = "", $emailAddress = "", $username = "", $providerName = "")
    {
        if($fullName) {
            $this->fullName = $fullName;
        }
        if($emailAddress) {
            $this->emailAddress = $emailAddress;
        }
        if($username) {
            $this->username = $username;
        }
        if($providerName) {
            $this->providerName = $providerName;
        }
        return $this;
    }
    public function getFullName()
    {
        return $this->fullName;
    }
    public function setFullName($name)
    {
        $this->fullName = $name;
        return $this;
    }
    public function getEmailAddress()
    {
        return $this->emailAddress;
    }
    public function setEmailAddress($email)
    {
        $this->emailAddress = $email;
        return $this;
    }
    public function getUsername()
    {
        return $this->username;
    }
    public function setUsername($username)
    {
        $this->username = $username;
        return $this;
    }
    public function getProviderName()
    {
        return $this->providerName;
    }
    public function setProviderName($provider)
    {
        $this->providerName = $provider;
        return $this;
    }
}

?>