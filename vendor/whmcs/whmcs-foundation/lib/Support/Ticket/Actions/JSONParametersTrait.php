<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Support\Ticket\Actions;

trait JSONParametersTrait
{
    public function unserializeParameters($string) : array
    {
        if(is_null($string)) {
            return [];
        }
        try {
            $decoded = json_decode($string, true);
        } catch (\Error $error) {
            return [];
        }
        if(json_last_error() !== JSON_ERROR_NONE) {
            return [];
        }
        return $decoded;
    }
    public function serializeParameters()
    {
        $encoded = json_encode($this->getParametersToSerialize());
        if($encoded === false) {
            return "";
        }
        return $encoded;
    }
    protected abstract function getParametersToSerialize() : array;
}

?>