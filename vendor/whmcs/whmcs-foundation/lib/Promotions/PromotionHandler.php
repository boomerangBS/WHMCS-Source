<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Promotions;

class PromotionHandler
{
    protected $view;
    protected $configuration;
    public function getConfiguration() : PromotionConfiguration
    {
        return $this->configuration;
    }
    public function setConfiguration(PromotionConfiguration $configuration) : \self
    {
        $this->configuration = $configuration;
        return $this;
    }
    public function getView() : PromotionViewInterface
    {
        return $this->view;
    }
    public function withView(PromotionViewInterface $view) : \self
    {
        $this->view = $view;
        return $this;
    }
    public function promotablePromotions() : \Illuminate\Support\Collection
    {
        $promotionObjs = new \Illuminate\Support\Collection();
        $promotions = $this->configuration->promotions();
        foreach ($promotions as $promotion) {
            $promotionObj = $this->instantiatePromotion($promotion["class"], $promotion)->setViewInstance($this->getView());
            if(!$promotionObj->isDismissed() && $promotionObj->isPromotable()) {
                $promotionObjs->put($promotionObj->getIdentifier(), $promotionObj);
            }
        }
        return $promotionObjs;
    }
    public function dismissPromotion($identifier)
    {
        return $this->assertValidPromotion($identifier)->dismiss();
    }
    private function assertValidPromotion($identifier) : AbstractPromotion
    {
        $promo = $this->configuration->promotionByIdentifier($identifier);
        return $this->instantiatePromotion($promo["class"], $promo);
    }
    private function instantiatePromotion($class, array $promotion) : AbstractPromotion
    {
        try {
            $action = NULL;
            if(isset($promotion["action"])) {
                $action = $promotion["action"];
            }
            return (new $class(...$promotion["classConstructorArguments"] ?? []))->setIdentifier($promotion["identifier"])->setTitle(\AdminLang::trans($promotion["title"]))->setDescription(\AdminLang::trans($promotion["description"]))->setAction($action)->setLogoUrl($promotion["logoUrl"] ?? NULL)->setDismissTTL($promotion["dismissTTL"]);
        } catch (\Throwable $e) {
            throw new \InvalidArgumentException("Unable to load configured class: " . $e->getMessage(), $e->getCode(), $e);
        }
    }
}

?>