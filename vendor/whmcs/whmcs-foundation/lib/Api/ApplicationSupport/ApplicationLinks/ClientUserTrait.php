<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Api\ApplicationSupport\ApplicationLinks;

trait ClientUserTrait
{
    private $user;
    private $client;
    public function setUserClient($user, $client)
    {
        if($user instanceof \WHMCS\User\User) {
            $this->user = $user;
        }
        if($client instanceof \WHMCS\User\Client) {
            $this->client = $client;
        }
    }
    public function getUser()
    {
        return $this->user;
    }
    public function getClient()
    {
        return $this->client;
    }
}

?>