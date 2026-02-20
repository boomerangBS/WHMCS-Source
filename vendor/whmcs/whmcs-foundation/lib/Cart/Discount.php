<?php

namespace WHMCS\Cart;

class Discount
{
    private $name;
    private $amount;
    public function __construct(string $name, \WHMCS\View\Formatter\Price $price)
    {
        $this->name = $name;
        $this->amount = $price;
    }
    public function getName()
    {
        return $this->name;
    }
    public function setName($name) : \self
    {
        $this->name = $name;
        return $this;
    }
    public function getAmount() : \WHMCS\View\Formatter\Price
    {
        return $this->amount;
    }
    public function setAmount(\WHMCS\View\Formatter\Price $amount) : \self
    {
        $this->amount = $amount;
        return $this;
    }
}

?>