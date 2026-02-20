<?php

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