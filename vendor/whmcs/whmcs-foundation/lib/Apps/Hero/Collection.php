<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Apps\Hero;

class Collection
{
    protected $heroes = [];
    public function __construct(array $data)
    {
        $this->heroes = $data;
    }
    public function get()
    {
        if(empty($this->heroes)) {
            return [];
        }
        $default = [];
        if(isset($this->heroes["default"])) {
            $default = $this->heroes["default"];
        } else {
            trigger_error("hero collection data lacks defaults", E_USER_WARNING);
        }
        $country = strtolower(\WHMCS\Config\Setting::getValue("DefaultCountry"));
        $heroes = $default;
        if(isset($this->heroes[$country])) {
            $heroes = $this->heroes[$country];
        }
        foreach ($heroes as $key => $values) {
            $heroes[$key] = new Model($values);
        }
        return $heroes;
    }
}

?>