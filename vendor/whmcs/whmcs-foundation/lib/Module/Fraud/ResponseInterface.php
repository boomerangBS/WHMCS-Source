<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Module\Fraud;

interface ResponseInterface
{
    public function __construct($jsonData, $httpCode);
    public function isSuccessful();
    public function getHttpCode();
    public function isEmpty();
    public function get($key);
    public function toArray();
    public function toJson();
}

?>