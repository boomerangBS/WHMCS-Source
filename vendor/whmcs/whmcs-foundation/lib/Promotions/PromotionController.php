<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Promotions;

class PromotionController
{
    protected $config;
    protected function getPromotionViews() : array
    {
        try {
            $promotions = (new PromotionHandler())->setConfiguration($this->getConfig())->withView(new PromotionViewer("admin.promotions.viewer"))->promotablePromotions();
        } catch (\InvalidArgumentException $e) {
            throw new \WHMCS\Exception\HttpCodeException("Unable to load promotion config: " . $e->getMessage(), 500, $e);
        }
        $promotionViews = [];
        foreach ($promotions as $promotion) {
            $promotionViews[] = $promotion->getView();
        }
        return $promotionViews;
    }
    public function renderPromotionCarousel()
    {
        $promotionViews = $this->getPromotionViews();
        if(empty($promotionViews)) {
            return "";
        }
        return view("admin.promotions.carousel", ["promotions" => $promotionViews]);
    }
    public function dismiss(\WHMCS\Http\Message\ServerRequest $request) : void
    {
        try {
            (new PromotionHandler())->setConfiguration($this->getConfig())->dismissPromotion($request->get("identifier", ""));
        } catch (\InvalidArgumentException $ex) {
            throw new \WHMCS\Exception\HttpCodeException("Promotion Dismissing Error: " . $ex->getMessage(), 404, $ex);
        }
    }
    protected function getConfig() : PromotionConfiguration
    {
        if(is_null($this->config)) {
            $this->config = new PromotionConfiguration();
        }
        return $this->config;
    }
}

?>