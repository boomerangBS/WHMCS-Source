<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Module\ConfigOption;

class ConfigOption implements \Illuminate\Contracts\Support\Arrayable
{
    private $name;
    private $type;
    private $options = [];
    private $size = 0;
    private $loader = "";
    private $simpleMode = false;
    private $description = "";
    private $default = "";
    public function getName()
    {
        return $this->name;
    }
    public function getType()
    {
        return $this->type;
    }
    public function getOptions() : array
    {
        return $this->options;
    }
    public function getSize() : int
    {
        return $this->size;
    }
    public function getLoader()
    {
        return $this->loader;
    }
    public function getDescription()
    {
        return $this->description;
    }
    public function getSimpleMode()
    {
        return $this->simpleMode;
    }
    public function getDefault()
    {
        return $this->default;
    }
    public function setName($name) : ConfigOption
    {
        $this->name = $name;
        return $this;
    }
    public function setType($type) : ConfigOption
    {
        $this->type = $type;
        return $this;
    }
    public function setOptions($options) : ConfigOption
    {
        $this->options = $options;
        return $this;
    }
    public function setSize($size) : ConfigOption
    {
        $this->size = $size;
        return $this;
    }
    public function setLoader($loader) : ConfigOption
    {
        $this->loader = $loader;
        $this->simpleMode = (bool) strlen($loader);
        return $this;
    }
    public function setDescription($description) : ConfigOption
    {
        $this->description = $description;
        return $this;
    }
    public function setSimpleMode($simpleMode) : ConfigOption
    {
        $this->simpleMode = $simpleMode;
        return $this;
    }
    public function setDefault($default) : ConfigOption
    {
        $this->default = $default;
        return $this;
    }
    public static function factory($name = "text", string $type = 40, int $size = "", string $description = [], array $options = "", string $default = "", string $loader = false, $simpleMode) : \self
    {
        if(!$name) {
            throw new \WHMCS\Exception\Module\InvalidConfiguration("ConfigOption name is required");
        }
        $option = new self();
        return $option->setName($name)->setType($type)->setSize($size)->setOptions($options)->setDefault($default)->setDescription($description)->setLoader($loader)->setSimpleMode($simpleMode);
    }
    public function toArray() : array
    {
        return [$this->getName() => ["Type" => $this->getType(), "Size" => $this->getSize(), "Default" => $this->getDefault(), "Description" => $this->getDescription(), "Options" => $this->getOptions(), "Loader" => $this->getLoader(), "SimpleMode" => $this->getSimpleMode()]];
    }
}

?>