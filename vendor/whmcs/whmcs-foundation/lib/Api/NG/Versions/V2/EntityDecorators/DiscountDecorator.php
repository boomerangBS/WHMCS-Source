<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Api\NG\Versions\V2\EntityDecorators;

class DiscountDecorator extends \WHMCS\Api\NG\Versions\V2\AbstractApiEntityDecorator
{
    public static function getEntityClass()
    {
        return "WHMCS\\Cart\\Discount";
    }
    protected function formatToArray($entity) : array
    {
        return ["name" => $entity->getName(), "amount" => ["value" => $entity->getAmount()->toNumeric(), "code" => $entity->getAmount()->getCurrency()->code]];
    }
}

?>