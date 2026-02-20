<?php

namespace WHMCS\Product\Pricing;

class Comparison
{
    protected $firstProduct;
    protected $secondProduct;
    protected $currency;
    public function __construct(\WHMCS\Product\Pricing $firstProduct = NULL, \WHMCS\Product\Pricing $secondProduct = NULL, \Currency $currency = NULL)
    {
        $this->firstProduct = $firstProduct;
        $this->secondProduct = $secondProduct;
        $this->currency = $currency;
        return $this;
    }
    public function setFirstProduct(\WHMCS\Product\Pricing $product)
    {
        $this->firstProduct = $product;
        return $this;
    }
    public function setSecondProduct(\WHMCS\Product\Pricing $product)
    {
        $this->secondProduct = $product;
        return $this;
    }
    public function firstIsGreater($cycle)
    {
        $price = $this->diff($cycle);
        if(!$price) {
            return true;
        }
        return 0 < $price->price()->getValue();
    }
    protected function canCompare()
    {
        if(is_null($this->firstProduct) || is_null($this->secondProduct)) {
            return false;
        }
        return true;
    }
    public function diff($cycle)
    {
        if(!$this->canCompare()) {
            return NULL;
        }
        if(is_null($this->firstProduct->byCycle($cycle)) || is_null($this->secondProduct->byCycle($cycle))) {
            return NULL;
        }
        $comparisonPriceDifference = $this->firstProduct->byCycle($cycle)->breakdownPriceNumeric() - $this->secondProduct->byCycle($cycle)->breakdownPriceNumeric();
        $setupFeeDifference = $this->firstProduct->byCycle($cycle)->setup()->getValue() - $this->secondProduct->byCycle($cycle)->setup()->getValue();
        return new Price(["cycle" => $cycle, "setupfee" => $setupFeeDifference, "price" => new \WHMCS\View\Formatter\Price($comparisonPriceDifference, $this->currency)]);
    }
}

?>