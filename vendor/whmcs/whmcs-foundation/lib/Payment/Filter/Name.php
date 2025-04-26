<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Payment\Filter;

class Name extends AbstractFilter
{
    private $acceptableName = [];
    public function __construct($name)
    {
        if(!is_array($name)) {
            $name = [$name];
        }
        $this->acceptableName = $name;
    }
    public function filter(\WHMCS\Payment\Adapter\AdapterInterface $adapter)
    {
        $name = $adapter->getName();
        if(in_array($name, $this->acceptableName)) {
            return true;
        }
        return false;
    }
}

?>