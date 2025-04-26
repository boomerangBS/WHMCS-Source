<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Api\NG\Versions\V2\EntityDecorators;

class PriceDecorator extends \WHMCS\Api\NG\Versions\V2\AbstractApiEntityDecorator
{
    public static function getEntityClass()
    {
        return "WHMCS\\Product\\Pricing\\Price";
    }
    protected function formatToArray($entity) : array
    {
        if($entity->isFree()) {
            return [];
        }
        $currencyCode = $entity->price()->getCurrency()["code"];
        return ["setup" => ["value" => $entity->setup()->toNumeric(), "code" => $currencyCode], "amount" => ["value" => $entity->price()->toNumeric(), "code" => $currencyCode]];
    }
}

?>