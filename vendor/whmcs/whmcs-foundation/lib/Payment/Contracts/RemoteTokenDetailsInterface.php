<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Payment\Contracts;

interface RemoteTokenDetailsInterface
{
    public function getRemoteToken();
    public function setRemoteToken($value);
    public function createRemote();
    public function updateRemote();
    public function deleteRemote();
    public function getBillingContactParamsForRemoteCall(\WHMCS\User\Contracts\UserInterface $client, \WHMCS\User\Contracts\ContactInterface $contact);
}

?>