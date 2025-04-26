<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

namespace WHMCS\Module\Gateway\paypal_ppcpv\API;

// Decoded file for php version 72.
class GenericErrorResponse extends AbstractErrorResponse
{
    public $error;
    public $error_description;
    public function error()
    {
        return $this->error;
    }
    public function message()
    {
        return $this->error_description;
    }
    public static function factory($json) : \self
    {
        $r = parent::factory($json);
        if(is_null($r->error) || is_null($r->error_description)) {
            return NULL;
        }
        return $r;
    }
    public function __toString()
    {
        return sprintf("(%s) %s", $this->error(), $this->message());
    }
}

?>