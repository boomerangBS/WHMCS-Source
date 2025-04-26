<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Authentication;

class CurrentUser
{
    public function isAuthenticatedUser()
    {
        return (bool) $this->user();
    }
    public function isAuthenticatedAdmin()
    {
        return (bool) $this->admin();
    }
    public function isMasqueradingAdmin()
    {
        return (bool) ($this->admin() && $this->client() && $this->user());
    }
    public function user() : \WHMCS\User\User
    {
        return \Auth::user();
    }
    public function admin() : \WHMCS\User\Admin
    {
        return \WHMCS\User\Admin::getAuthenticatedUser();
    }
    public function client() : \WHMCS\User\Client
    {
        return \Auth::client();
    }
}

?>