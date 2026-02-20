<?php

namespace WHMCS\Order;

class ContainsReferralProductsMask extends \WHMCS\Utility\Bitmask
{
    const BIT_COUNT = 2;
    const HAS_RECOMMENDATION_ITEM = 1;
    const HAS_UPSELL_ITEM = 2;
    const HAS_ALL = NULL;
    public function setRecommendationItem($state) : \self
    {
        $this->as(self::HAS_RECOMMENDATION_ITEM, $state);
        return $this;
    }
    public function setUpsellItem($state) : \self
    {
        $this->as(self::HAS_UPSELL_ITEM, $state);
        return $this;
    }
    public function hasRecommendationItems()
    {
        return $this->has(self::HAS_RECOMMENDATION_ITEM);
    }
    public function hasUpsellItems()
    {
        return $this->has(self::HAS_UPSELL_ITEM);
    }
}

?>