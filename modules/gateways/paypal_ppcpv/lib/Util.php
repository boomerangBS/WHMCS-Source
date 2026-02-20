<?php


namespace WHMCS\Module\Gateway\paypal_ppcpv;
class Util
{
    public static function safeLoadCurrencyId(string $currencyCode)
    {
        if(empty($currencyCode)) {
            return 0;
        }
        $currency = \WHMCS\Billing\Currency::where("code", $currencyCode)->first();
        if(is_null($currency)) {
            return 0;
        }
        return $currency->id;
    }
    public static function decodeJSON(string $json)
    {
        $decoded = json_decode($json);
        if(json_last_error() !== JSON_ERROR_NONE) {
            return false;
        }
        return $decoded;
    }
    public static function overlayMapOnObject($map, $o)
    {
        $map = (object) $map;
        foreach (get_object_vars($o) as $property => $v) {
            if(property_exists($map, $property)) {
                $o->{$property} = $map->{$property};
            }
        }
        return $o;
    }
    public static function deepCopy($to, &$from) : void
    {
        if(is_object($from)) {
            if(!is_object($to)) {
                throw new \InvalidArgumentException("to must be the same type as from");
            }
            foreach ($from as $property => $value) {
                $to->{$property} = is_object($value) ? (object) [] : NULL;
                Util::deepCopy($to->{$property}, $value);
            }
        } else {
            $to = $from;
        }
    }
    public static function getAndDeleteSession($key = NULL, string $prefix)
    {
        if(is_null($prefix)) {
            return \WHMCS\Session::getAndDelete($key);
        }
        $value = \WHMCS\Session::get($key);
        $prefixLength = strlen($prefix);
        if(substr($value, 0, $prefixLength) === $prefix) {
            \WHMCS\Session::delete($key);
            return substr($value, $prefixLength);
        }
        return "";
    }
}

?>