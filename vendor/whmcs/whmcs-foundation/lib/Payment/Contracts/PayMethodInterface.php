<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Payment\Contracts;

interface PayMethodInterface extends \WHMCS\User\Contracts\ContactAwareInterface, PayMethodTypeInterface
{
    public function payment();
    public function isDefaultPayMethod();
    public function setAsDefaultPayMethod();
    public function getDescription();
    public function setDescription($value);
    public function getGateway();
    public function setGateway(\WHMCS\Module\Gateway $value);
    public function isUsingInactiveGateway();
    public function getPaymentDescription();
    public function save(array $options);
}

?>