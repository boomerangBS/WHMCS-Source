<?php

namespace WHMCS\Authentication\Contracts;

interface Token
{
    public static function factoryFromUser(\WHMCS\User\User $user);
    public function validFormat();
    public function id() : int;
    public function generate();
    public function generateHash();
    public function validateUser(\WHMCS\User\User $user, $validateIp) : \WHMCS\User\User;
}

?>