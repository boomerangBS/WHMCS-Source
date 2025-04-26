<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Payment\Contracts;

interface SensitiveDataInterface
{
    public function getEncryptionKey();
    public function wipeSensitiveData();
    public function getSensitiveDataAttributeName();
    public function getSensitiveProperty($property);
    public function setSensitiveProperty($property, $value);
    public function getSensitiveData();
}

?>