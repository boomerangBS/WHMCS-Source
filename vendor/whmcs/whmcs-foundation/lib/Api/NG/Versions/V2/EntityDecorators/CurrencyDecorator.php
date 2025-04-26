<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Api\NG\Versions\V2\EntityDecorators;

class CurrencyDecorator extends \WHMCS\Api\NG\Versions\V2\AbstractApiEntityDecorator
{
    public static function getEntityClass()
    {
        return "WHMCS\\Billing\\Currency";
    }
    protected function formatToArray($entity) : array
    {
        return ["code" => strtoupper($entity->code), "prefix" => $entity->prefix, "suffix" => $entity->suffix, "default" => (bool) $entity->default];
    }
}

?>