<?php

namespace WHMCS\Product\Observers;

class AddonOnDemandRenewalObserver
{
    public function deleted(\WHMCS\Product\Addon $addon)
    {
        $addon->overrideOnDemandRenewal()->delete();
    }
}

?>