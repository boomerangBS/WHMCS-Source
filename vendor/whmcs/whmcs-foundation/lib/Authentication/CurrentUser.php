<?php

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