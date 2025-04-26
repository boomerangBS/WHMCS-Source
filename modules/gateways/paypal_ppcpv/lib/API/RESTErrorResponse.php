<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

namespace WHMCS\Module\Gateway\paypal_ppcpv\API;

// Decoded file for php version 72.
class RESTErrorResponse extends AbstractErrorResponse
{
    public $name = "";
    public $message = "";
    public $debug_id = "";
    public $details = [];
    public $links = [];
    public function error()
    {
        return $this->name;
    }
    public function message()
    {
        return $this->message;
    }
    public function traceId()
    {
        return (string) $this->debug_id;
    }
    public static function factory(string $json)
    {
        $r = parent::factory($json);
        if(is_null($r->name) || is_null($r->message)) {
            return NULL;
        }
        return self::factoryName($r);
    }
    protected static function factoryName(\self $restError) : \self
    {
        $className = "WHMCS\\Module\\Gateway\\paypal_ppcpv\\API\\RESTError" . str_replace(" ", "", ucwords(str_replace("_", " ", strtolower($restError->name))));
        if(class_exists($className)) {
            return \WHMCS\Module\Gateway\paypal_ppcpv\Util::overlayMapOnObject($restError, new $className());
        }
        return $restError;
    }
    public function __toString()
    {
        return sprintf("(%s) %s [trace: %s]", $this->error(), $this->message(), $this->traceId());
    }
}

?>