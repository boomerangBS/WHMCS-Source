<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Module\ConfigOption;

class ConfigOptionList implements \Illuminate\Contracts\Support\Arrayable
{
    private $options = [];
    public function add(ConfigOption $configOption) : ConfigOptionList
    {
        $this->options[] = $configOption;
        return $this;
    }
    public function toArray() : array
    {
        $return = [];
        foreach ($this->options as $option) {
            $return += $option->toArray();
        }
        return $return;
    }
}

?>