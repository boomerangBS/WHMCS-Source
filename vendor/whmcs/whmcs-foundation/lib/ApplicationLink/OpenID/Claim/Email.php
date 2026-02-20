<?php

namespace WHMCS\ApplicationLink\OpenID\Claim;

class Email extends AbstractClaim
{
    public $email;
    public $email_verified;
    public function hydrate()
    {
        $user = $this->getUser();
        $this->email = $user->email;
        $this->email_verified = false;
        return $this;
    }
}

?>