<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Service\Ssl;

abstract class ValidationMethod
{
    protected $method;
    public abstract function populate($values) : \self;
    public abstract function methodNameConstant();
    public abstract function friendlyName();
    public abstract function translationKey(\WHMCS\Language\AbstractLanguage $language) : \WHMCS\Language\AbstractLanguage;
    public abstract function defaults() : \self;
    public function __construct()
    {
        $this->method = $this->methodNameConstant();
    }
    public static function factory($method) : \self
    {
        $expectedClass = "WHMCS\\Service\\Ssl\\ValidationMethod" . ucfirst($method);
        if(!class_exists($expectedClass)) {
            throw new \WHMCS\Exception("Unknown method");
        }
        return new $expectedClass();
    }
    public static function factoryFromPacked($value) : \self
    {
        $unpacked = static::unpack($value);
        $method = static::sanitizeMethodIdentifier($unpacked->method ?? "");
        if(strlen($method) == 0) {
            throw new \WHMCS\Exception("Indistinguishable method");
        }
        return static::factory($method)->populate($unpacked);
    }
    public function is($methodConstant)
    {
        return $this->method === $methodConstant;
    }
    public static function sanitizeMethodIdentifier($ident)
    {
        return substr(preg_replace("/[^[:alpha:]]/", "", $ident), 0, 12);
    }
    public static function unpack(string $packed)
    {
        if(empty($packed)) {
            throw new \WHMCS\Exception("Nothing to unpack");
        }
        $unpacked = json_decode($packed);
        if(!is_null($unpacked) && json_last_error() === JSON_ERROR_NONE) {
            return $unpacked;
        }
        throw new \WHMCS\Exception("Failed to unpack");
    }
    public function pack()
    {
        $objectClassProperties = [];
        foreach (get_class_vars(get_class($this)) as $property => $value) {
            if($this->{$property} === NULL) {
            } else {
                $objectClassProperties[$property] = $this->{$property};
            }
        }
        return json_encode((object) $objectClassProperties);
    }
    public function populateFromClassProperties($values) : ValidationMethod
    {
        $properties = array_keys(get_class_vars(get_class($this)));
        foreach ($properties as $property) {
            $value = NULL;
            if(property_exists($values, $property)) {
                $value = $values->{$property};
            }
            $this->{$property} = $value;
        }
        return $this;
    }
}

?>