<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Module\Registrar\CentralNic;

trait ParametersTrait
{
    public function getParam(string $key, $default = "")
    {
        if($this->hasParam($key)) {
            return $this->params[$key];
        }
        return $default;
    }
    public function getParamArray($key) : array
    {
        return (array) $this->getParam($key, []);
    }
    public function getParamString($key)
    {
        return (string) $this->getParam($key);
    }
    public function getAsciiParam($key)
    {
        $asciiParam = $this->getParamString($key . "_punycode");
        if(!empty($asciiParam)) {
            return $asciiParam;
        }
        return $this->getParamString($key);
    }
    public function getParamFloat($key)
    {
        return (double) $this->getParam($key, 0);
    }
    public function getParamInt($key) : int
    {
        return (int) $this->getParam($key, 0);
    }
    public function hasParam($key)
    {
        return isset($this->params[$key]);
    }
    public function isParamEnabled($key)
    {
        return $this->isEnabled($this->getParam($key));
    }
    public function isEnabled($value)
    {
        return $value == "on" || $value == "1" || $value;
    }
    public function getArrayValueArray($key, array $array) : array
    {
        return (array) $this->getArrayValue($key, $array, []);
    }
    public function getArrayValueString($key, array $array) : array
    {
        return (string) $this->getArrayValue($key, $array);
    }
    public function getArrayValue(string $key, array $array, $default = "")
    {
        if(isset($array[$key])) {
            return $array[$key];
        }
        return $default;
    }
}

?>