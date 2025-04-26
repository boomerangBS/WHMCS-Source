<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Api\NG\Versions\V2\EntityDecorators;

trait PricingDecoratorTrait
{
    protected function decoratePricing(\WHMCS\Product\PricedEntityInterface $entity) : array
    {
        $entityData["pricing"]["is_free"] = false;
        if($entity->isFree()) {
            $entityData["pricing"]["is_free"] = true;
        } elseif($entity->isOneTime()) {
            $entityData["pricing"]["onetime"] = \WHMCS\Api\NG\Versions\V2\ApiEntityDecoratorFactory::decorate($entity->pricing()->onetime());
        } else {
            foreach ($entity->pricing()->allAvailableCycles() as $cyclePrice) {
                $entityData["pricing"]["recurring"][] = array_merge(["cycle" => $cyclePrice->cycle()], \WHMCS\Api\NG\Versions\V2\ApiEntityDecoratorFactory::decorate($cyclePrice));
            }
        }
        return $entityData;
    }
}

?>