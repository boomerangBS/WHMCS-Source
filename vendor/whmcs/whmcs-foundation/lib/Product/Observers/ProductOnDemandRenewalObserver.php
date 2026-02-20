<?php

namespace WHMCS\Product\Observers;

class ProductOnDemandRenewalObserver
{
    public function deleted(\WHMCS\Product\Product $product)
    {
        $product->overrideOnDemandRenewal()->delete();
    }
}

?>