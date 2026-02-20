<?php

namespace WHMCS\Service\Adapters;

class AbstractProductAdapter
{
    protected $product;
    public static function factory(\WHMCS\Product\Product $product) : \self
    {
        $self = new static();
        $self->product = $product;
        return $self;
    }
}

?>