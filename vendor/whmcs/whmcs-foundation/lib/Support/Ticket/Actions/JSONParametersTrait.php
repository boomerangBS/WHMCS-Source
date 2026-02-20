<?php

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