<?php

namespace WHMCS\Cart;

class TaxTotal
{
    private $amount;
    private $description;
    private $percentage = 0;
    public function __construct(string $description, $percentage, \WHMCS\View\Formatter\Price $taxTotalAmount)
    {
        $this->setDescription($description ?: "Tax")->setPercentage($percentage)->setAmount($taxTotalAmount);
    }
    public function getAmount() : \WHMCS\View\Formatter\Price
    {
        if(!is_null($this->amount)) {
            return $this->amount;
        }
        return new \WHMCS\View\Formatter\Price(0);
    }
    public function setAmount(\WHMCS\View\Formatter\Price $amount) : \self
    {
        $this->amount = $amount;
        return $this;
    }
    public function getDescription()
    {
        return $this->description;
    }
    public function setDescription($description) : \self
    {
        $this->description = $description;
        return $this;
    }
    public function getPercentage()
    {
        return $this->percentage;
    }
    public function setPercentage($percent) : \self
    {
        $this->percentage = $percent;
        return $this;
    }
}

?>