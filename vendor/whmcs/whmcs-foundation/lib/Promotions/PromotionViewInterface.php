<?php

namespace WHMCS\Promotions;

interface PromotionViewInterface
{
    public function view(AbstractPromotion $promotion) : AbstractPromotion;
}

?>