<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Api\NG\Versions\V2\EntityDecorators;

class ProductDecorator extends \WHMCS\Api\NG\Versions\V2\AbstractApiEntityDecorator
{
    use PricingDecoratorTrait;
    public static function getEntityClass()
    {
        return "WHMCS\\Product\\Product";
    }
    protected function formatToArray($entity) : array
    {
        $productData = $entity->only(["id", "name", "description"]);
        if(trim($productData["description"]) === "") {
            unset($productData["description"]);
        }
        if($entity instanceof \WHMCS\Product\PricedEntityInterface) {
            $productData = array_merge($productData, $this->decoratePricing($entity));
        }
        return $productData;
    }
}

?>