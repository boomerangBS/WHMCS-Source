<?php


namespace WHMCS;
class AddonPricing extends Pricing
{
    protected function getCycleData($cycle = 0, int $months) : array
    {
        return $this->getCycleBaseData($cycle, $months);
    }
}

?>