<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Exception\Authentication;

class RequiresSecondFactor extends AbstractAuthenticationException
{
    private $user;
    public static function createForUser(\WHMCS\User\User $user)
    {
        $self = new static();
        $self->user = $user;
        return $self;
    }
    public function getUser() : \WHMCS\User\User
    {
        return $this->user;
    }
}

?>