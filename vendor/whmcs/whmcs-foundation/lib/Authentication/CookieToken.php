<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Authentication;

class CookieToken extends SessionToken
{
    protected $requiredKeys = ["id", "email", "password", "remember_token", "userip", "timestamp", "hash"];
    public static function factoryFromUser(\WHMCS\User\User $user)
    {
        $self = new static();
        return $self->setData(["id" => $user->id, "email" => $user->email, "password" => static::hashValueForStorage($user->password), "remember_token" => $user->newRememberToken(), "userip" => $user->currentIp(), "timestamp" => time()]);
    }
    private function rememberToken()
    {
        return $this->data["remember_token"];
    }
    public function validateUser(\WHMCS\User\User $user = true, $validateIp) : \WHMCS\User\User
    {
        if(!parent::validateUser($user, $validateIp)) {
            return false;
        }
        if($this->rememberToken() !== $user->rememberToken) {
            return false;
        }
        return true;
    }
}

?>