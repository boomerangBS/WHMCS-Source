<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Payment\Contracts;

interface PayMethodAdapterInterface extends \WHMCS\User\Contracts\ContactAwareInterface, PayMethodTypeInterface, SensitiveDataInterface
{
    public function payMethod();
    public static function factoryPayMethod(\WHMCS\User\Contracts\UserInterface $client, \WHMCS\User\Contracts\ContactInterface $billingContact, $description);
    public function getDisplayName();
}

?>