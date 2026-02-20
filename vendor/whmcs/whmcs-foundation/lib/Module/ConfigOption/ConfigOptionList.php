<?php

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