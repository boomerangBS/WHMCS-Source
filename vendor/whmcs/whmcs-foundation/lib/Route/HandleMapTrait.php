<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Route;

trait HandleMapTrait
{
    protected $routes = [];
    public abstract function getMappedAttributeName();
    public function mapRoute($route)
    {
        $attributeName = $this->getMappedAttributeName();
        if(empty($route["handle"]) || empty($route[$attributeName])) {
            return $this;
        }
        $this->routes[serialize($route["handle"])] = $route[$attributeName];
        return $this;
    }
    public function getMappedRoute($key)
    {
        if(!is_null($key) && !$key instanceof \Closure) {
            if(is_array($key) || is_object($key)) {
                $key = serialize($key);
            }
            if(isset($this->routes[$key])) {
                return $this->routes[$key];
            }
        }
    }
}

?>