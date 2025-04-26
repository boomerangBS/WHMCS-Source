<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
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