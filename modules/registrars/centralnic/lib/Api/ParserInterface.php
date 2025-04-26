<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Module\Registrar\CentralNic\Api;

interface ParserInterface
{
    public function buildPayload($params) : array;
    public function parseResponse($response) : array;
    public function getResponseDataValue(string $key, array $data);
    public function getResponseCode($response) : int;
    public function getResponseDescription($response) : array;
    public function getResponseData($response) : array;
}

?>