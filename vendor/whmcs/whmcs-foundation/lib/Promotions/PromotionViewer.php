<?php

namespace WHMCS\Promotions;

class PromotionViewer implements PromotionViewInterface
{
    protected $resourceView = "";
    public function __construct(string $resourceView)
    {
        $this->resourceView = $resourceView;
    }
    public function view(AbstractPromotion $promotion) : AbstractPromotion
    {
        return view($this->resourceView, ["promotion" => $promotion]);
    }
}

?>