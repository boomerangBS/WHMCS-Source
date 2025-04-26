<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\ApplicationLink\OpenID\Claim;

abstract class AbstractClaim
{
    protected $user;
    protected $claimName;
    public function __construct(\WHMCS\User\UserInterface $user, $claimName = NULL)
    {
        $this->setUser($user);
        if($claimName) {
            $this->setClaimName($claimName);
        }
        $this->hydrate();
    }
    public function getUser()
    {
        return $this->user;
    }
    public function setUser(\WHMCS\User\UserInterface $user)
    {
        $this->user = $user;
        return $this;
    }
    public function getClaimName()
    {
        return $this->claimName;
    }
    public function setClaimName($claimName)
    {
        $this->claimName = $claimName;
        return $this;
    }
    public function toArray()
    {
        $data = [];
        $properties = get_object_vars($this);
        foreach ($properties as $propName => $propValue) {
            if($propName == "user" || $propName == "claimName") {
            } else {
                $data[$propName] = $propValue;
            }
        }
        return $data;
    }
    protected abstract function hydrate();
}

?>