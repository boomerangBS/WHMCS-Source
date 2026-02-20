<?php

namespace WHMCS\Config;

abstract class AbstractConfig extends \ArrayObject
{
    protected $defaultValue = "";
    public function __construct(array $data = [])
    {
        parent::setFlags(parent::ARRAY_AS_PROPS);
        parent::__construct($data);
    }
    public function setData(array $data)
    {
        $this->exchangeArray($data);
        return $this;
    }
    public function getData()
    {
        return $this->getArrayCopy();
    }
    public function setDefaultReturnValue($value)
    {
        $this->defaultValue = $value;
    }
    public function offsetGet($property)
    {
        if($this->offsetExists($property)) {
            return parent::offsetGet($property);
        }
        return $this->defaultValue;
    }
}

?>